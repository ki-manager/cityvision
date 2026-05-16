import streamlit as st
from utils.components import card

def show():
    st.title("Beschreibung des Projekts")

    card(
        "Kernidee",
        "Im Projekt CityVision werden Smart-City-, Umwelt- und Stadtdaten "
        "untersucht, analysiert und visualisiert. Die Plattform verarbeitet "
        "Daten aus unterschiedlichen Quellen wie Wetterdaten, "
        "Verkehrsanalysen und Luftqualitätswerten."
    )

    card(
        "Grundlage für KI & ML",
        "CityVision schafft eine strukturierte Datenbasis, auf der zukünftig "
        "Machine-Learning-Modelle, Prognosen und KI-Assistenten aufbauen können."
    )
