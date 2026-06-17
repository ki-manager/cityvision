import os
import pandas as pd
import streamlit as st
import matplotlib.pyplot as plt

from sklearn.linear_model import (
    LinearRegression
)


# =====================================================
# Streamlit
# =====================================================

st.set_page_config(

    page_title="CityVision Vorhersagen",

    layout="wide"

)


# =====================================================
# Titel
# =====================================================

st.title("Vorhersagen")

st.subheader(
    "Alle Messwerte der Station "
    "Braunschweig Stadt"
)


# =====================================================
# Messwertnamen
# =====================================================

measurement_mapping = {

    "TT": {

        "name": "Temperatur",

        "unit": "°C",

        "category": "Wetter"

    },

    "RF": {

        "name": "Relative Luftfeuchte",

        "unit": "%",

        "category": "Wetter"

    },

    "PP": {

        "name": "Luftdruck",

        "unit": "hPa",

        "category": "Wetter"

    },

    "PPP": {

        "name": "Luftdruck",

        "unit": "hPa",

        "category": "Wetter"

    },

    "FF": {

        "name": "Windgeschwindigkeit",

        "unit": "m/s",

        "category": "Wetter"

    },

    "DD": {

        "name": "Windrichtung",

        "unit": "°",

        "category": "Wetter"

    },

    "RR": {

        "name": "Niederschlag",

        "unit": "mm",

        "category": "Wetter"

    },

    "O3": {

        "name": "Ozon",

        "unit": "µg/m³",

        "category": "Luft"

    },

    "NO": {

        "name": "Stickstoffmonoxid",

        "unit": "µg/m³",

        "category": "Luft"

    },

    "NO2": {

        "name": "Stickstoffdioxid",

        "unit": "µg/m³",

        "category": "Luft"

    },

    "SO2": {

        "name": "Schwefeldioxid",

        "unit": "µg/m³",

        "category": "Luft"

    },

    "CO": {

        "name": "Kohlenmonoxid",

        "unit": "mg/m³",

        "category": "Luft"

    },

    "PM1024": {

        "name": "Feinstaub PM10",

        "unit": "µg/m³",

        "category": "Luft"

    },

    "PM2524": {

        "name": "Feinstaub PM2.5",

        "unit": "µg/m³",

        "category": "Luft"

    },

    "GS": {

        "name": "Globalstrahlung",

        "unit": "W/m²",

        "category": "Strahlung"

    },

    "G": {

        "name": "Globalstrahlung",

        "unit": "W/m²",

        "category": "Strahlung"

    },

    "UV": {

        "name": "UV-Index",

        "unit": "",

        "category": "Strahlung"

    },

    "RD": {

        "name": "Windrichtung",

        "unit": "Grad",

        "category": "wind"

    },

    "TF": {

        "name": "Taupunkttemperatur",

        "unit": "°C",

        "category": "Wetter"

    }

}


# =====================================================
# Datei
# =====================================================

FILE_PATH = "data/stations/BGSW.csv"


# =====================================================
# Prüfen
# =====================================================

if not os.path.exists(FILE_PATH):

    st.error(
        "Datei BGSW.csv nicht gefunden"
    )

    st.stop()


# =====================================================
# CSV laden
# =====================================================

try:

    df = pd.read_csv(
        FILE_PATH
    )

except Exception as error:

    st.error(
        "Fehler beim Laden"
    )

    st.text(str(error))

    st.stop()


# =====================================================
# Spalten prüfen
# =====================================================

required_columns = [

    "Zeitindex",
    "Komponente",
    "Messwert"

]


missing_columns = [

    col for col in required_columns

    if col not in df.columns

]


if missing_columns:

    st.error(
        f"Fehlende Spalten: "
        f"{missing_columns}"
    )

    st.stop()


# =====================================================
# Datentypen
# =====================================================

df["Messwert"] = pd.to_numeric(

    df["Messwert"],

    errors="coerce"

)


# =====================================================
# Fehlende Werte entfernen
# =====================================================

df = df.dropna(
    subset=["Messwert"]
)


# =====================================================
# Komponenten
# =====================================================

components = sorted(
    df["Komponente"].unique()
)


# =====================================================
# Übersicht
# =====================================================

component_names = []


for component in components:

    component_info = measurement_mapping.get(

        component,

        {
            "name": component,
            "unit": "",
            "category": "Unbekannt"
        }

    )


    component_names.append(

        f"{component_info['name']} "
        f"({component})"

    )



# =====================================================
# Alle Messwerte anzeigen
# =====================================================

for component in components:

    # =============================================
    # Informationen
    # =============================================

    component_info = measurement_mapping.get(

        component,

        {
            "name": component,
            "unit": "",
            "category": "Unbekannt"
        }

    )


    component_name = component_info[
        "name"
    ]

    component_unit = component_info[
        "unit"
    ]

    component_category = component_info[
        "category"
    ]


    # =============================================
    # Filtern
    # =============================================

    component_df = df[

        df["Komponente"]
        == component

    ]


    # =============================================
    # Prüfen
    # =============================================

    if len(component_df) < 5:

        continue


    # =============================================
    # Features
    # =============================================

    X = component_df[[

        "Zeitindex"

    ]]


    y = component_df[
        "Messwert"
    ]


    # =============================================
    # Modell
    # =============================================

    model = LinearRegression()

    model.fit(X, y)


    # =============================================
    # Vorhersage
    # =============================================

    predictions = model.predict(X)


    # =============================================
    # Zukunft
    # =============================================

    future_index = pd.DataFrame({

        "Zeitindex":

            range(

                int(
                    component_df[
                        "Zeitindex"
                    ].max()
                ) + 1,

                int(
                    component_df[
                        "Zeitindex"
                    ].max()
                ) + 11

            )

    })


    future_predictions = model.predict(
        future_index
    )


    # =============================================
    # Abstand
    # =============================================

    st.markdown("---")


    # =============================================
    # Titel
    # =============================================

    st.subheader(

        f"{component_name} "
        f"({component})"

    )


    # =============================================
    # Informationen
    # =============================================

    st.info(

        f"""
        Kategorie:
        {component_category}

        Einheit:
        {component_unit}
        """

    )


    # =============================================
    # Statistik
    # =============================================

    col1, col2, col3 = st.columns(3)


    col1.metric(

        "Minimum",

        round(
            y.min(),
            2
        )

    )


    col2.metric(

        "Maximum",

        round(
            y.max(),
            2
        )

    )


    col3.metric(

        "Mittelwert",

        round(
            y.mean(),
            2
        )

    )


    # =============================================
    # Diagramm
    # =============================================

    fig, ax = plt.subplots(
        figsize=(14, 5)
    )


    # =============================================
    # Originalwerte
    # =============================================

    ax.plot(

        component_df[
            "Zeitindex"
        ],

        y,

        linewidth=2,

        label="Messwerte"

    )


    # =============================================
    # Regression
    # =============================================

    ax.plot(

        component_df[
            "Zeitindex"
        ],

        predictions,

        linestyle="dashed",

        linewidth=2,

        label="Regression"

    )


    # =============================================
    # Zukunft
    # =============================================

    ax.plot(

        future_index[
            "Zeitindex"
        ],

        future_predictions,

        linestyle="dotted",

        linewidth=3,

        label="Vorhersage"

    )


    # =============================================
    # Titel
    # =============================================

    ax.set_title(

        f"{component_name} "
        f"Vorhersage"

    )


    ax.set_xlabel(
        "Zeitindex"
    )

    ax.set_ylabel(
        component_unit
    )

    ax.grid(True)

    ax.legend()


    # =============================================
    # Anzeigen
    # =============================================

    st.pyplot(fig)


    # =============================================
    # Vorhersagen
    # =============================================

    prediction_df = pd.DataFrame({

        "Zeitindex":

            future_index[
                "Zeitindex"
            ],

        "Vorhersage":

            future_predictions

    })

