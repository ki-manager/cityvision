import streamlit as st
from utils.components import card

def show():
    st.title("Zielsetzung")

    c1, c2, c3 = st.columns(3)

    with c1:
        card(
            "01 · Visualisierung",
            "Klare Darstellung von Wetter, Luft, Verkehr, Energie "
            "und Umwelt in Dashboards."
        )

    with c2:
        card(
            "02 · Zusammenhänge",
            "Korrelationen, Trends, Muster und Auffälligkeiten erkennen."
        )

    with c3:
        card(
            "03 · Modulare Plattform",
            "Erweiterbar um ML, Echtzeitdaten, KI-Assistenten und Prognosen."
        )
