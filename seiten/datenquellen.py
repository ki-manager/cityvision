import streamlit as st
from utils.components import card

def show():
    st.title("Datenquellen")

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
