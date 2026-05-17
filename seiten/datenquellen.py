import streamlit as st
from utils.components import card

def show():
    st.title("Datenquellen")

    card(
            "Datenbeschaffung",
            "Die Plattform nutzt unterschiedliche öffentliche und frei verfügbare Datenquellen. "
            "Die Daten werden automatisiert eingelesen, verarbeitet und in eine einheitliche Struktur überführt. Dadurch können Informationen kombiniert und gemeinsamanalysiert werden."
        )

    quellen = [
        ("🌦️ Wetterdaten", "Temperatur, Niederschlag, Wind, Luftdruck"),
        ("🚦 Verkehrsanalysen", "Verkehrsaufkommen, Staus, Fließgeschwindigkeit"),
        ("🌫️ Luftqualität", "Feinstaub, PM10, NO₂, O₃, CO"),
        ("⚡ Energieverbrauch", "Strom, Gas, Wärme, Tagesprofile"),
        ("🌳 Umweltindikatoren", "Lärm, Biodiversität, Grünflächen"),
        ("🏙️ Stadtdaten", "Bevölkerung, Mobilität, Infrastruktur"),
    ]

    cols = st.columns(3)

    for i, (t, b) in enumerate(quellen):
        with cols[i % 3]:
            card(t, b)
