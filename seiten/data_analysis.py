import os
import glob
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
# Streamlit Konfiguration
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
    "Analyse der bereinigten Stationsdaten"
)


# =====================================================
# Datenordner
# =====================================================

DATA_DIR = "data/stations"


# =====================================================
# Prüfen ob Ordner existiert
# =====================================================

if not os.path.exists(DATA_DIR):

    st.error(
        "Ordner data/stations existiert nicht."
    )

    st.stop()


# =====================================================
# Stationsordner laden
# =====================================================

stations = sorted([

    folder for folder in os.listdir(
        DATA_DIR
    )

    if os.path.isdir(
        os.path.join(
            DATA_DIR,
            folder
        )
    )

])


# =====================================================
# Prüfen ob Stationen existieren
# =====================================================

if len(stations) == 0:

    st.warning(
        "Keine Stationen gefunden."
    )

    st.stop()


# =====================================================
# Stationsnamen erzeugen
# =====================================================

station_display = {}

for station in stations:

    mapping = station_mapping.get(

        station,

        {
            "name": station,
            "typ": "Unbekannt",
            "region": "Unbekannt"
        }

    )

    display_name = (

        f"{mapping['name']} "
        f"({mapping['typ']})"

    )

    station_display[
        display_name
    ] = station


# =====================================================
# Station auswählen
# =====================================================

selected_display = st.selectbox(

    "Station auswählen",

    sorted(
        station_display.keys()
    )

)


# =====================================================
# Stationscode
# =====================================================

selected_station = station_display[
    selected_display
]


# =====================================================
# Stationsinformationen
# =====================================================

station_info = station_mapping.get(

    selected_station,

    {
        "name": selected_station,
        "typ": "Unbekannt",
        "region": "Unbekannt"
    }

)


# =====================================================
# Stationsinformationen anzeigen
# =====================================================

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
# Stationsordner
# =====================================================

station_path = os.path.join(

    DATA_DIR,
    selected_station

)


# =====================================================
# CSV-Dateien laden
# =====================================================

csv_files = glob.glob(

    os.path.join(
        station_path,
        "*.csv"
    )

)


# =====================================================
# metadata.csv entfernen
# =====================================================

csv_files = [

    file for file in csv_files

    if "metadata.csv"
    not in file

]


# =====================================================
# Prüfen ob CSV-Dateien existieren
# =====================================================

if len(csv_files) == 0:

    st.warning(
        "Keine CSV-Dateien gefunden."
    )

    st.stop()


# =====================================================
# Komponenten vorbereiten
# =====================================================

component_mapping = {}

component_display = {}


for file in csv_files:

    filename = os.path.basename(
        file
    )

    component = (
        filename
        .replace(
            f"{selected_station}_",
            ""
        )
        .replace(".csv", "")
    )

    component_mapping[
        component
    ] = file


    # =================================================
    # Wettermapping
    # =================================================

    mapping = weather_mapping.get(

        component,

        {
            "name": component,
            "beschreibung":
                "Keine Beschreibung",
            "einheit": "",
            "kategorie":
                "Unbekannt"
        }

    )

    display_name = (

        f"{mapping['name']} "
        f"({component})"

    )

    component_display[
        display_name
    ] = component


# =====================================================
# Prüfen ob Komponenten existieren
# =====================================================

if len(component_display) == 0:

    st.warning(
        "Keine Komponenten gefunden."
    )

    st.stop()


# =====================================================
# Komponente auswählen
# =====================================================

selected_component_display = st.selectbox(

    "Messwert auswählen",

    sorted(
        component_display.keys()
    )

)


# =====================================================
# Komponenten-Code
# =====================================================

selected_component = component_display[
    selected_component_display
]


# =====================================================
# Komponenteninformationen
# =====================================================

component_info = weather_mapping.get(

    selected_component,

    {
        "name": selected_component,
        "beschreibung":
            "Keine Beschreibung",
        "einheit": "",
        "kategorie":
            "Unbekannt"
    }

)


# =====================================================
# Komponenteninformationen anzeigen
# =====================================================

st.info(

    f"""
    Messwert:
    {component_info['name']}

    Kategorie:
    {component_info['kategorie']}

    Beschreibung:
    {component_info['beschreibung']}

    Einheit:
    {component_info['einheit']}
    """

)


# =====================================================
# Datei laden
# =====================================================

selected_file = component_mapping[
    selected_component
]


# =====================================================
# CSV laden
# =====================================================

try:

    df = pd.read_csv(
        selected_file
    )

except Exception as error:

    st.error(
        f"Fehler beim Laden:"
        f" {selected_file}"
    )

    st.text(str(error))

    st.stop()


# =====================================================
# Prüfen ob Daten vorhanden
# =====================================================

if df.empty:

    st.warning(
        "Datei enthält keine Daten."
    )

    st.stop()


# =====================================================
# Daten bereinigen
# =====================================================

df = df.dropna(
    subset=["Messwert"]
)

df = df.drop_duplicates()


# =====================================================
# Datentypen korrigieren
# =====================================================

df["Messwert"] = pd.to_numeric(

    df["Messwert"],

    errors="coerce"

)


df = df.dropna(
    subset=["Messwert"]
)


# =====================================================
# Übersicht
# =====================================================

st.success(
    f"{len(df)} Datensätze geladen"
)


# =====================================================
# Datenvorschau
# =====================================================

st.subheader("Datenvorschau")

st.dataframe(
    df.head(20)
)


# =====================================================
# Statistik
# =====================================================

st.subheader("Statistik")


col1, col2, col3 = st.columns(3)


col1.metric(

    "Mittelwert",

    round(
        df["Messwert"].mean(),
        2
    )

)


col2.metric(

    "Minimum",

    round(
        df["Messwert"].min(),
        2
    )

)


col3.metric(

    "Maximum",

    round(
        df["Messwert"].max(),
        2
    )

)


# =====================================================
# Diagramm
# =====================================================

st.subheader("Messwertverlauf")


fig, ax = plt.subplots(
    figsize=(14, 6)
)


ax.plot(

    df["Zeitindex"],

    df["Messwert"],

    linewidth=2

)


ax.set_title(

    f"{station_info['name']} - "
    f"{component_info['name']}"

)


ax.set_xlabel(
    "Zeitindex"
)

ax.set_ylabel(
    component_info["einheit"]
)

ax.grid(True)


st.pyplot(fig)


# =====================================================
# Datenqualität
# =====================================================

st.subheader("Datenqualität")


quality_col1, quality_col2 = st.columns(2)


quality_col1.metric(

    "Fehlende Werte",

    int(
        df["Messwert"]
        .isna()
        .sum()
    )

)


quality_col2.metric(

    "Datensätze",

    len(df)

)


# =====================================================
# Rohdaten
# =====================================================

with st.expander(
    "Rohdaten anzeigen"
):

    st.dataframe(df)


# =====================================================
# CSV Export
# =====================================================

csv_export = df.to_csv(
    index=False
)


st.download_button(

    label="CSV herunterladen",

    data=csv_export,

    file_name=(
        f"{selected_station}_"
        f"{selected_component}.csv"
    ),

    mime="text/csv"

)