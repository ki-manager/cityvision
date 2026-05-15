import streamlit as st
from utils.components import card

def show():
    st.title("Plattform")

    cols = st.columns(4)

    module = [
        ("🤖 KI", "Machine Learning"),
        ("⚡ Echtzeit", "Live-Daten"),
        ("💬 Assistent", "Chatbots"),
        ("🔮 Prognosen", "Vorhersagen"),
    ]

    for col, (t, b) in zip(cols, module):
        with col:
            card(t, b)
