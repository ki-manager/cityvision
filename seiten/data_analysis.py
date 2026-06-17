import os
import pandas as pd
import streamlit as st
import matplotlib.pyplot as plt

from modules.api.station_mapping import (
    station_mapping
)

from modules.api.weather_mapping import (
    weather_mapping
)


# =====================================================
# Streamlit
# =====================================================

st.set_page_config(

    page_title="CityVision",

    layout="wide"

)


# =====================================================
# Titel
# =====================================================

st.title("Datenanalyse")

st.subheader(
    "Analyse aller Messpunkte"
)


# =====================================================
# Datenordner
# =====================================================

DATA_DIR = "data/stations"


# =====================================================
# Prüfen
# =====================================================

if not os.path.exists(DATA_DIR):

    st.error(
        "Ordner data/stations existiert nicht."
    )

    st.stop()


# =====================================================
# Stationsdateien laden
# =====================================================

station_files = sorted([

    file for file in os.listdir(
        DATA_DIR
    )

    if file.endswith(".csv")

    and file != "metadata.csv"

])


# =====================================================
# Prüfen
# =====================================================

if len(station_files) == 0:

    st.warning(
        "Keine Stationsdateien gefunden"
    )

    st.stop()


# =====================================================
# Stationsnamen erzeugen
# =====================================================

station_options = []


for file in station_files:

    station_code = file.replace(
        ".csv",
        ""
    )

    mapping = station_mapping.get(

        station_code,

        {
            "name": station_code,
            "typ": "Unbekannt",
            "region": "Unbekannt"
        }

    )

    display_name = (

        f"{mapping['name']} "
        f"({mapping['typ']}) "
        f"- {station_code}"

    )

    station_options.append({

        "label": label,

        "value": station_code

    })


# =====================================================
# Auswahl
# =====================================================

selected_display = st.selectbox(

    "Station auswählen",

    station_options,

    format_func=lambda x: x["label"]

)


selected_station = (
    selected_station_option["value"]
)


# =====================================================
# Stationsinfo
# =====================================================

station_info = station_mapping.get(

    selected_station,

    {
        "name": selected_station,
        "typ": "Unbekannt",
        "region": "Unbekannt"
    }

)


st.info(

    f"""
    Station:
    {station_info['name']}

    Typ:
    {station_info['typ']}

    Region:
    {station_info['region']}
    """

)


# =====================================================
# CSV-Datei laden
# =====================================================

csv_path = os.path.join(

    DATA_DIR,

    f"{selected_station}.csv"

)


# =====================================================
# CSV lesen
# =====================================================

try:

    df = pd.read_csv(
        csv_path
    )

except Exception as error:

    st.error(
        "Fehler beim Laden"
    )

    st.text(str(error))

    st.stop()


# =====================================================
# Prüfen
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
# Komponenten
# =====================================================

components = sorted(
    df["Komponente"].unique()
)


# =====================================================
# Übersicht
# =====================================================

st.subheader(
    "Messpunktübersicht"
)


overview_rows = []


for component in components:

    component_df = df[

        df["Komponente"]
        == component

    ]


    mapping = weather_mapping.get(

        component,

        {
            "name": component,
            "einheit": "",
            "kategorie": "Unbekannt"
        }

    )


    missing_values = int(
        component_df["Messwert"]
        .isna()
        .sum()
    )


    clean_df = component_df.dropna(
        subset=["Messwert"]
    )


    if clean_df.empty:

        continue


    mean = clean_df[
        "Messwert"
    ].mean()


    std = clean_df[
        "Messwert"
    ].std()


    outlier_count = 0


    if (
        pd.notna(std)
        and std > 0
    ):

        outliers = clean_df[

            (
                clean_df["Messwert"]
                < mean - 3 * std
            )

            |

            (
                clean_df["Messwert"]
                > mean + 3 * std
            )

        ]

        outlier_count = len(
            outliers
        )


    overview_rows.append({

        "Messwert":
            mapping["name"],

        "Code":
            component,

        "Kategorie":
            mapping["kategorie"],

        "Einheit":
            mapping["einheit"],

        "Datensätze":
            len(clean_df),

        "Fehlende Werte":
            missing_values,

        "Ausreißer":
            outlier_count,

        "Mittelwert":
            round(mean, 2)

    })


# =====================================================
# Übersicht DataFrame
# =====================================================

overview_df = pd.DataFrame(
    overview_rows
)


st.dataframe(
    overview_df
)


# =====================================================
# Diagramm Fehlende Werte
# =====================================================

st.subheader(
    "Fehlende Werte"
)


fig1, ax1 = plt.subplots(
    figsize=(14, 5)
)


ax1.bar(

    overview_df["Code"],

    overview_df[
        "Fehlende Werte"
    ]

)


ax1.grid(True)

ax1.set_xlabel(
    "Messpunkt"
)

ax1.set_ylabel(
    "Anzahl"
)

st.pyplot(fig1)


# =====================================================
# Diagramm Ausreißer
# =====================================================

st.subheader(
    "Ausreißer"
)


fig2, ax2 = plt.subplots(
    figsize=(14, 5)
)


ax2.bar(

    overview_df["Code"],

    overview_df[
        "Ausreißer"
    ]

)


ax2.grid(True)

ax2.set_xlabel(
    "Messpunkt"
)

ax2.set_ylabel(
    "Anzahl"
)

st.pyplot(fig2)


# =====================================================
# Messpunkte
# =====================================================

st.subheader(
    "Alle Messpunkte"
)


for component in components:

    mapping = weather_mapping.get(

        component,

        {
            "name": component,
            "beschreibung": "",
            "einheit": ""
        }

    )


    component_df = df[

        df["Komponente"]
        == component

    ]


    component_df = component_df.dropna(
        subset=["Messwert"]
    )


    if component_df.empty:

        continue


    with st.expander(

        f"{mapping['name']} "
        f"({component})"

    ):


        st.write(

            f"""
            Kategorie:
            {mapping.get('kategorie', '')}

            Einheit:
            {mapping.get('einheit', '')}

            Beschreibung:
            {mapping.get('beschreibung', '')}
            """

        )


        # =============================================
        # Diagramm
        # =============================================

        fig, ax = plt.subplots(
            figsize=(14, 5)
        )


        ax.plot(

            component_df["Zeitindex"],

            component_df["Messwert"],

            linewidth=2

        )


        ax.set_title(
            mapping["name"]
        )

        ax.set_xlabel(
            "Zeitindex"
        )

        ax.set_ylabel(
            mapping.get(
                "einheit",
                ""
            )
        )

        ax.grid(True)


        st.pyplot(fig)


        # =============================================
        # Statistik
        # =============================================

        col1, col2, col3 = st.columns(3)


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
                component_df[
                    "Messwert"
                ].mean(),
                2
            )

        )


        # =============================================
        # Rohdaten
        # =============================================

        st.dataframe(
            component_df
        )