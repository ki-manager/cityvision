import streamlit as st
from utils.components import card

def show():
    st.title("Zielsetzung")

    c1, c2, c3 = st.columns(3)

    with c1:
        card("Visualisierung", "Diagramme und Dashboards")

    with c2:
        card("Zusammenhänge", "Korrelationen erkennen")

    with c3:
        card("Plattform", "Erweiterbar mit KI")
