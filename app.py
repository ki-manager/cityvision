import streamlit as st

st.set_page_config(page_title="CityVision", layout="wide")

st.sidebar.title("CityVision")

menu = st.sidebar.selectbox(
    "Navigation",
    ["Startseite", "Datenanalyse", "Lineare Regression", "Random Forest"]
)

if menu == "Startseite":
    import seiten.start
elif menu == "Datenanalyse":
    import seiten.data_analysis
elif menu == "Lineare Regression":
    import seiten.predictions
elif menu == "Random Forest":
    import seiten.random_forest
