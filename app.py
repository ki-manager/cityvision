import streamlit as st

st.set_page_config(page_title="CityVision", layout="wide")

st.sidebar.title("CityVision")

menu = st.sidebar.selectbox(
    "Navigation",
    ["Startseite", "Datenanalyse", "Ausreißer", "Vorhersagen"]
)

if menu == "Startseite":
    import seiten.start
elif menu == "Datenanalyse":
    import seiten.data_analysis
elif menu == "Ausreißer":
    import seiten.outliers
elif menu == "Vorhersagen":
    import seiten.predictions
