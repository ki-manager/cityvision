import os
import pandas as pd
import streamlit as st
import matplotlib.pyplot as plt

from sklearn.ensemble import (
    RandomForestRegressor
)

from sklearn.metrics import (
    mean_absolute_error,
    mean_squared_error,
    r2_score
)

from modules.api.weather_mapping import (
    weather_mapping
)


# =====================================================
# Streamlit
# =====================================================

st.set_page_config(

    page_title="CityVision Random Forest",

    layout="wide"

)


# =====================================================
# Titel
# =====================================================

st.title("Random Forest Regression")

st.subheader(
    "Vorhersagen für Braunschweig Stadt"
)


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
# Alle Messwerte
# =====================================================

for component in components:

    # =============================================
    # Mapping
    # =============================================

    mapping = weather_mapping.get(

        component,

        {
            "name": component,
            "einheit": "",
            "kategorie": "Unbekannt",
            "beschreibung": ""
        }

    )


    component_name = mapping[
        "name"
    ]

    component_unit = mapping[
        "einheit"
    ]

    component_category = mapping[
        "kategorie"
    ]

    component_description = mapping[
        "beschreibung"
    ]


    # =============================================
    # Filtern
    # =============================================

    component_df = df[

        df["Komponente"]
        == component

    ].copy()


    # =============================================
    # Prüfen
    # =============================================

    if len(component_df) < 10:

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

    model = RandomForestRegressor(

        n_estimators=100,

        random_state=42

    )


    # =============================================
    # Training
    # =============================================

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
    # Fehler
    # =============================================

    mae = mean_absolute_error(
        y,
        predictions
    )

    mse = mean_squared_error(
        y,
        predictions
    )

    r2 = r2_score(
        y,
        predictions
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

        Beschreibung:
        {component_description}
        """

    )


    # =============================================
    # Statistik
    # =============================================

    col1, col2, col3, col4 = st.columns(4)


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


    col4.metric(

        "R² Score",

        round(
            r2,
            3
        )

    )


    # =============================================
    # Fehlerwerte
    # =============================================

    st.write(

        f"""
        MAE:
        {round(mae, 3)}

        MSE:
        {round(mse, 3)}
        """

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
    # Random Forest
    # =============================================

    ax.plot(

        component_df[
            "Zeitindex"
        ],

        predictions,

        linestyle="dashed",

        linewidth=2,

        label="Random Forest"

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
        f"Random Forest Vorhersage"

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


