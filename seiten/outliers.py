import os
import pandas as pd
import streamlit as st
import matplotlib.pyplot as plt

from modules.api.weather_mapping import (
    weather_mapping
)


# =====================================================
# Streamlit
# =====================================================

st.set_page_config(

    page_title="CityVision Datenanalyse",

    layout="wide"

)


# =====================================================
# Titel
# =====================================================

st.title("Datenanalyse")

st.subheader(
    "Ausreißeranalyse "
    "Braunschweig Stadt"
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
# Übersicht
# =====================================================

st.subheader(
    "Verfügbare Messwerte"
)


component_names = []


for component in components:

    mapping = weather_mapping.get(

        component,

        {
            "name": component
        }

    )

    component_names.append(

        f"{mapping['name']} "
        f"({component})"

    )


st.write(component_names)


# =====================================================
# Alle Komponenten anzeigen
# =====================================================

for component in components:

    # =============================================
    # Mapping
    # =============================================

    mapping = weather_mapping.get(

        component,

        {
            "name": component,
            "beschreibung": "",
            "einheit": "",
            "kategorie": ""
        }

    )


    component_name = mapping["name"]

    component_unit = mapping["einheit"]

    component_category = mapping["kategorie"]


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

    if len(component_df) < 5:

        continue


    # =============================================
    # Statistik
    # =============================================

    mean = component_df[
        "Messwert"
    ].mean()


    std = component_df[
        "Messwert"
    ].std()


    # =============================================
    # Ausreißer
    # =============================================

    lower = mean - 3 * std

    upper = mean + 3 * std


    outliers = component_df[

        (
            component_df["Messwert"]
            < lower
        )

        |

        (
            component_df["Messwert"]
            > upper
        )

    ]


    normal_values = component_df[

        (
            component_df["Messwert"]
            >= lower
        )

        &

        (
            component_df["Messwert"]
            <= upper
        )

    ]


    # =============================================
    # Abstand
    # =============================================

    st.markdown("---")


    # =============================================
    # Überschrift
    # =============================================

    st.subheader(

        f"{component_name} "
        f"({component})"

    )


    # =============================================
    # Informationen
    # =============================================

    st.caption(

        f"Kategorie: "
        f"{component_category}"

    )


    st.info(

        f"""
        Einheit:
        {component_unit}

        Mittelwert:
        {round(mean, 2)}

        Standardabweichung:
        {round(std, 2)}

        Ausreißer:
        {len(outliers)}
        """

    )


    # =============================================
    # Statistik
    # =============================================

    col1, col2, col3, col4 = st.columns(4)


    col1.metric(

        "Minimum",

        round(
            component_df[
                "Messwert"
            ].min(),
            2
        )

    )


    col2.metric(

        "Maximum",

        round(
            component_df[
                "Messwert"
            ].max(),
            2
        )

    )


    col3.metric(

        "Mittelwert",

        round(
            mean,
            2
        )

    )


    col4.metric(

        "Ausreißer",

        len(outliers)

    )


    # =============================================
    # Grafik
    # =============================================

    fig, ax = plt.subplots(
        figsize=(14, 5)
    )


    # =============================================
    # Normale Werte
    # =============================================

    ax.scatter(

        normal_values[
            "Zeitindex"
        ],

        normal_values[
            "Messwert"
        ],

        s=12,

        label="Normale Werte"

    )


    # =============================================
    # Ausreißer
    # =============================================

    if not outliers.empty:

        ax.scatter(

            outliers[
                "Zeitindex"
            ],

            outliers[
                "Messwert"
            ],

            s=40,

            label="Ausreißer"

        )


    # =============================================
    # Mittelwert
    # =============================================

    ax.axhline(

        mean,

        linestyle="dashed",

        linewidth=2,

        label="Mittelwert"

    )


    # =============================================
    # Grenzen
    # =============================================

    ax.axhline(

        upper,

        linestyle="dotted",

        linewidth=2,

        label="Obere Grenze"

    )


    ax.axhline(

        lower,

        linestyle="dotted",

        linewidth=2,

        label="Untere Grenze"

    )


    # =============================================
    # Titel
    # =============================================

    ax.set_title(

        f"Ausreißeranalyse "
        f"{component_name}"

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
    # Ausreißer anzeigen
    # =============================================

    st.subheader(
        "Gefundene Ausreißer"
    )


    if outliers.empty:

        st.success(
            "Keine Ausreißer gefunden"
        )

    else:

        st.dataframe(
            outliers
        )


    # =============================================
    # Rohdaten
    # =============================================

    st.subheader(
        "Rohdaten"
    )

    st.dataframe(
        component_df
    )