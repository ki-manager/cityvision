import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

from utils.components import stat

def show():
    st.title("Live-Dashboard")
    st.caption("Interaktive Demo mit simulierten Smart-City-Daten.")

    quelle = st.multiselect(
        "Datenquellen wählen",
        ["Temperatur", "Feinstaub", "Verkehr", "Energie"],
        default=["Temperatur", "Feinstaub", "Verkehr"],
    )

    tage = st.slider("Zeitraum (Tage)", 7, 90, 30)

    days = pd.date_range(end=pd.Timestamp.today(), periods=tage)

    np.random.seed(42)

    df = pd.DataFrame({
        "Datum": days,
        "Temperatur": 15 + 8*np.sin(np.linspace(0, 4, tage)) + np.random.randn(tage),
        "Feinstaub": 18 + 6*np.cos(np.linspace(0, 5, tage)) + np.random.randn(tage)*2,
        "Verkehr": 800 + 200*np.sin(np.linspace(0, 6, tage)) + np.random.randn(tage)*40,
        "Energie": 120 + 25*np.cos(np.linspace(0, 5, tage)) + np.random.randn(tage)*5,
    })

    c1, c2, c3, c4 = st.columns(4)

    with c1:
        stat(f"{df['Temperatur'].mean():.1f}°C", "Ø Temperatur")

    with c2:
        stat(f"{df['Feinstaub'].mean():.1f}", "Ø Feinstaub")

    with c3:
        stat(f"{df['Verkehr'].mean():.0f}", "Ø Verkehr")

    with c4:
        stat(f"{df['Energie'].mean():.0f}", "Ø Energie")

    if quelle:

        fig, ax = plt.subplots(figsize=(12, 5))

        for q in quelle:
            sns.lineplot(
                data=df,
                x="Datum",
                y=q,
                label=q,
                ax=ax
            )

        ax.set_title("Zeitreihe")
        ax.set_xlabel("Datum")

        plt.xticks(rotation=45)

        st.pyplot(fig)

    with st.expander("Rohdaten anzeigen"):
        st.dataframe(df, width='stretch')