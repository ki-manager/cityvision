import streamlit as st
import pandas as pd
import matplotlib.pyplot as plt

from modules.api.luen_api import (
    LuenAPI
)

from modules.prediction.predictor import (
    Predictor
)

from modules.api.weather_mapping import (
    weather_mapping
)


# =====================================================
# Titel
# =====================================================

st.title("Vorhersagen")

st.subheader(
    "KI-basierte Umwelt- und Wetterprognosen"
)


# =====================================================
# Daten laden
# =====================================================

try:
    api = LuenAPI()

    data = api.download_data()

    df = api.create_dataframe(data)

except Exception as e:
    st.error(f"Fehler beim Laden der Daten: {str(e)}")
    st.stop()


# =====================================================
# Region auswählen
# =====================================================

regions = sorted(
    df["Region"].unique()
)

if not regions:
    st.error("Keine Regionen in den Daten gefunden.")
    st.stop()

selected_region = st.selectbox(

    "Region auswählen",

    regions

)


# =====================================================
# Nach Region filtern
# =====================================================

region_df = df[
    df["Region"]
    == selected_region
]


# =====================================================
# Station auswählen
# =====================================================

stations = sorted(
    region_df["Station"].unique()
)

if not stations:
    st.error(f"Keine Stationen für die Region '{selected_region}' gefunden.")
    st.stop()

selected_station = st.selectbox(

    "Messstation auswählen",

    stations

)


# =====================================================
# Nach Station filtern
# =====================================================

station_df = region_df[

    region_df["Station"]
    == selected_station

]


# =====================================================
# Messkomponente auswählen
# =====================================================

components = sorted(
    station_df["Komponente"].unique()
)

if not components:
    st.error(f"Keine Messkomponenten für die Station '{selected_station}' gefunden.")
    st.stop()

selected_component = st.selectbox(

    "Messwert auswählen",

    components

)


# =====================================================
# Mapping Informationen
# =====================================================

mapping = weather_mapping.get(

    selected_component,

    {

        "name": selected_component,
        "beschreibung": "Keine Beschreibung",
        "einheit": "",
        "kategorie": "Unbekannt"

    }

)


# =====================================================
# Informationen anzeigen
# =====================================================

st.info(

    f"""
    Name: {mapping['name']}

    Kategorie: {mapping['kategorie']}

    Beschreibung:
    {mapping['beschreibung']}

    Einheit:
    {mapping['einheit']}
    """

)


# =====================================================
# Daten filtern
# =====================================================

filtered_df = station_df[

    station_df["Komponente"]
    == selected_component

]


# =====================================================
# Messwerte extrahieren
# =====================================================

values = filtered_df[
    "Messwert"
].values


# =====================================================
# Prüfen ob Daten vorhanden
# =====================================================

if len(values) < 5:

    st.warning(
        "Zu wenige Daten für Vorhersagen."
    )

else:

    # =================================================
    # Vorhersage erzeugen
    # =================================================

    predictor = Predictor()

    predictions = (
        predictor.create_prediction(
            values
        )
    )


    # =================================================
    # Vorhersage DataFrame
    # =================================================

    future_df = pd.DataFrame({

        "Vorhersage":
            predictions

    })


    # =================================================
    # Statistik
    # =====================================================

    st.subheader("Statistik")

    col1, col2, col3 = st.columns(3)

    col1.metric(

        "Mittelwert",

        round(
            values.mean(),
            2
        )

    )

    col2.metric(

        "Maximum",

        round(
            values.max(),
            2
        )

    )

    col3.metric(

        "Minimum",

        round(
            values.min(),
            2
        )

    )


    # =================================================
    # Vorhersagetabelle
    # =====================================================

    st.subheader(
        "Zukünftige Vorhersagen"
    )

    st.dataframe(
        future_df
    )


    # =================================================
    # Diagramm
    # =====================================================

    fig, ax = plt.subplots(
        figsize=(14, 6)
    )


    # =================================================
    # Originalwerte
    # =====================================================

    ax.plot(

        values,

        label="Messwerte",

        linewidth=2

    )


    # =================================================
    # Zukunftsachse
    # =====================================================

    future_x = range(

        len(values),

        len(values)
        + len(predictions)

    )


    # =================================================
    # Vorhersagen
    # =====================================================

    ax.plot(

        future_x,

        predictions,

        label="Vorhersage",

        linestyle="dashed",
        linewidth=2

    )


    # =================================================
    # Titel
    # =====================================================

    ax.set_title(

        f"{mapping['name']} "
        f"Vorhersage - "
        f"{selected_station}"

    )


    # =================================================
    # Achsen
    # =====================================================

    ax.set_xlabel(
        "Zeit"
    )

    ax.set_ylabel(
        mapping["einheit"]
    )


    # =================================================
    # Legende
    # =====================================================

    ax.legend()


    # =================================================
    # Grid
    # =====================================================

    ax.grid(True)


    # =================================================
    # Diagramm anzeigen
    # =====================================================

    st.pyplot(fig)


    # =================================================
    # Umweltbewertung
    # =====================================================

    st.subheader(
        "Automatische Bewertung"
    )


    latest_prediction = predictions[-1]


    # =================================================
    # PM10 Bewertung
    # =====================================================

    if selected_component == "PM1024":

        if latest_prediction < 20:

            st.success(
                "Sehr gute Luftqualität"
            )

        elif latest_prediction < 40:

            st.info(
                "Gute Luftqualität"
            )

        elif latest_prediction < 60:

            st.warning(
                "Erhöhte Feinstaubbelastung"
            )

        else:

            st.error(
                "Kritische Feinstaubwerte"
            )


    # =================================================
    # Ozon Bewertung
    # =====================================================

    elif selected_component == "O3":

        if latest_prediction < 60:

            st.success(
                "Niedrige Ozonbelastung"
            )

        elif latest_prediction < 120:

            st.warning(
                "Erhöhte Ozonwerte"
            )

        else:

            st.error(
                "Hohe Ozonbelastung"
            )


    # =================================================
    # Temperatur Bewertung
    # =====================================================

    elif selected_component == "TT":

        if latest_prediction < 5:

            st.info(
                "Kalte Wetterlage"
            )

        elif latest_prediction < 25:

            st.success(
                "Normale Temperaturen"
            )

        elif latest_prediction < 32:

            st.warning(
                "Heiße Wetterlage"
            )

        else:

            st.error(
                "Extreme Hitze"
            )