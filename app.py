import streamlit as st
from pathlib import Path

from pages import (
    start,
    kernidee,
    datenquellen,
    zielsetzung,
    visualisierung,
    zusammenhaenge,
    plattform,
    dashboard,
    fazit,
)

st.set_page_config(
    page_title="CityVision",
    page_icon="🏙️",
    layout="wide",
    initial_sidebar_state="expanded",
)

css_file = Path("assets/css/style.css")

with open(css_file, "r", encoding="utf-8") as f:
    st.markdown(f"<style>{f.read()}</style>", unsafe_allow_html=True)

menu = {
    "Start": start.show,
    "Kernidee": kernidee.show,
    "Datenquellen": datenquellen.show,
    "Zielsetzung": zielsetzung.show,
    "Visualisierung": visualisierung.show,
    "Zusammenhänge": zusammenhaenge.show,
    "Plattform": plattform.show,
    "Live-Dashboard": dashboard.show,
    "Fazit": fazit.show,
}

st.sidebar.title("CityVision")

selection = st.sidebar.radio(
    "Navigation",
    list(menu.keys())
)

menu[selection]()

st.markdown(
    "<div class='footer'>© 2026 CityVision – Thorsten Höke</div>",
    unsafe_allow_html=True,
)
