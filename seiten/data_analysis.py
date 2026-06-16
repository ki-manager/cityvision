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
# Prüfen ob Datenordner existiert
# =====================================================

if not os.path.exists(DATA_DIR):

    st.error(
        "Ordner data/stations nicht gefunden"
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
        "Keine Stationen gefunden"
    )

    st.stop()


# =====================================================
# Stationsoptionen
# =====================================================

station_options = []


for station in stations:

    mapping = station_mapping.get(

        station,

        {
            "name": station,
            "typ": "Unbekannt",
            "region": "Unbekannt"
        }

    )

    label = (

        f"{mapping['name']} "
        f"({mapping['typ']}) "
        f"- {station}"

    )

    station_options.append({

        "label": label,

        "value": station

    })


# =====================================================
# Station auswählen
# =====================================================

selected_station_option = st.selectbox(

    "Station auswählen",

    station_options,

    format_func=lambda x: x["label"]

)


# =====================================================
# Stationscode
# =====================================================

selected_station = (
    selected_station_option["value"]
)


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

    Kürzel:
    {selected_station}
    """

)


# =====================================================
# Stationspfad
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
# Prüfen
# =====================================================

if len(csv_files) == 0:

    st.warning(
        "Keine CSV-Dateien gefunden"
    )

    st.stop()


# =====================================================
# Analyse aller Messpunkte
# =====================================================

analysis_rows = []

component_file_mapping = {}

component_options = []


for file in csv_files:

    try:

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


        # =============================================
        # Dateimapping
        # =============================================

        component_file_mapping[
            component
        ] = file


        # =============================================
        # Mapping
        # =============================================

        mapping = weather_mapping.get(

            component,

            {
                "name": component,
                "einheit": "",
                "kategorie": "Unbekannt"
            }

        )


        # =============================================
        # Komponentenoption
        # =============================================

        component_options.append({

            "label":

                f"{mapping['name']} "
                f"({component})",

            "value":
                component

        })


        # =============================================
        # CSV laden
        # =============================================

        df = pd.read_csv(file)


        # =============================================
        # Prüfen
        # =============================================

        if "Messwert" not in df.columns:

            continue


        # =============================================
        # Datentypen korrigieren
        # =============================================

        df["Messwert"] = pd.to_numeric(

            df["Messwert"],

            errors="coerce"

        )


        # =============================================
        # Statistik
        # =============================================

        original_count = len(df)

        missing_values = int(
            df["Messwert"]
            .isna()
            .sum()
        )


        clean_df = df.dropna(
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


        # =============================================
        # Ausreißer
        # =============================================

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


        # =============================================
        # Analyse speichern
        # =============================================

        analysis_rows.append({

            "Messwert":
                mapping["name"],

            "Code":
                component,

            "Kategorie":
                mapping["kategorie"],

            "Einheit":
                mapping["einheit"],

            "Datensätze":
                original_count,

            "Fehlende Werte":
                missing_values,

            "Ausreißer":
                outlier_count,

            "Mittelwert":
                round(mean, 2)
                if pd.notna(mean)
                else 0

        })

    except Exception as error:

        st.error(
            f"Fehler in Datei: {file}"
        )

        st.text(str(error))


# =====================================================
# Analyse DataFrame
# =====================================================

analysis_df = pd.DataFrame(
    analysis_rows
)


# =====================================================
# Prüfen
# =====================================================

if analysis_df.empty:

    st.warning(
        "Keine gültigen Daten"
    )

    st.stop()


# =====================================================
# Übersicht
# =====================================================

st.subheader(
    "Übersicht aller Messpunkte"
)

st.dataframe(
    analysis_df
)


# =====================================================
# Fehlende Werte Diagramm
# =====================================================

st.subheader(
    "Fehlende Werte"
)


fig1, ax1 = plt.subplots(
    figsize=(14, 6)
)


ax1.bar(

    analysis_df["Code"],

    analysis_df[
        "Fehlende Werte"
    ]

)


ax1.set_title(
    "Fehlende Werte pro Messpunkt"
)

ax1.set_xlabel(
    "Messpunkt"
)

ax1.set_ylabel(
    "Anzahl"
)

ax1.grid(True)


st.pyplot(fig1)


# =====================================================
# Ausreißer Diagramm
# =====================================================

st.subheader(
    "Ausreißer"
)


fig2, ax2 = plt.subplots(
    figsize=(14, 6)
)


ax2.bar(

    analysis_df["Code"],

    analysis_df[
        "Ausreißer"
    ]

)


ax2.set_title(
    "Ausreißer pro Messpunkt"
)

ax2.set_xlabel(
    "Messpunkt"
)

ax2.set_ylabel(
    "Anzahl"
)

ax2.grid(True)


st.pyplot(fig2)


# =====================================================
# Detailauswahl
# =====================================================

st.subheader(
    "Messpunkt Details"
)


selected_component_option = st.selectbox(

    "Messpunkt auswählen",

    component_options,

    format_func=lambda x: x["label"]

)


# =====================================================
# Komponentencode
# =====================================================

selected_component = (
    selected_component_option["value"]
)


# =====================================================
# Datei laden
# =====================================================

selected_file = (
    component_file_mapping.get(
        selected_component
    )
)


# =====================================================
# Prüfen
# =====================================================

if selected_file is None:

    st.warning(
        "Keine Datei gefunden"
    )

    st.stop()


# =====================================================
# CSV laden
# =====================================================

try:

    df = pd.read_csv(
        selected_file
    )

except Exception as error:

    st.error(
        "Fehler beim Laden "
        "der Detaildaten"
    )

    st.text(str(error))

    st.stop()


# =====================================================
# Datentypen korrigieren
# =====================================================

if "Messwert" not in df.columns:

    st.error(
        "Messwert-Spalte fehlt"
    )

    st.stop()


df["Messwert"] = pd.to_numeric(

    df["Messwert"],

    errors="coerce"

)


# =====================================================
# Fehlende entfernen
# =====================================================

df = df.dropna(
    subset=["Messwert"]
)


# =====================================================
# Prüfen
# =====================================================

if df.empty:

    st.warning(
        "Keine gültigen Daten vorhanden"
    )

    st.stop()


# =====================================================
# Zeitindex prüfen
# =====================================================

if "Zeitindex" not in df.columns:

    df["Zeitindex"] = range(
        len(df)
    )


# =====================================================
# Detaildiagramm
# =====================================================

st.subheader(
    "Messwertverlauf"
)


fig3, ax3 = plt.subplots(
    figsize=(14, 6)
)


ax3.plot(

    df["Zeitindex"],

    df["Messwert"],

    linewidth=2

)


ax3.set_title(

    f"{selected_station} - "
    f"{selected_component}"

)

ax3.set_xlabel(
    "Zeitindex"
)

ax3.set_ylabel(
    "Messwert"
)

ax3.grid(True)


st.pyplot(fig3)


# =====================================================
# Rohdaten
# =====================================================

with st.expander(
    "Rohdaten anzeigen"
):

    st.dataframe(df)