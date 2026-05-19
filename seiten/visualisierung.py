import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

def show():
    st.title("Visualisierung")
    st.text("Beispielhafte Diagramme moderner Dashboards")

    days = pd.date_range("2025-05-01", periods=30)

    df = pd.DataFrame({
        "Datum": days,
        "Temperatur (°C)": 15 + 8*np.sin(np.linspace(0, 3, 30)) + np.random.randn(30),
        "Feinstaub (µg/m³)": 18 + 6*np.cos(np.linspace(0, 4, 30)) + np.random.randn(30)*2,
        "Verkehr (Fz/h)": 800 + 200*np.sin(np.linspace(0, 6, 30)) + np.random.randn(30)*40,
        "Energie (MWh)": 120 + 25*np.cos(np.linspace(0, 5, 30)) + np.random.randn(30)*5,
    })

    c1, c2 = st.columns(2)

    # ---------- Wetter ----------
    with c1:

        fig, ax = plt.subplots(figsize=(8, 4))

        sns.lineplot(
            data=df,
            x="Datum",
            y="Temperatur (°C)",
            ax=ax
        )

        ax.set_title("Wetter")
        ax.set_ylabel("Temperatur (°C)")

        plt.xticks(rotation=45)

        st.pyplot(fig)

        # ---------- Verkehr ----------
        fig, ax = plt.subplots(figsize=(8, 4))

        sns.barplot(
            data=df,
            x=df["Datum"].dt.strftime("%d.%m"),
            y="Verkehr (Fz/h)",
            ax=ax
        )

        ax.set_title("Verkehr")
        ax.set_ylabel("Fahrzeuge / Stunde")

        plt.xticks(rotation=90)

        st.pyplot(fig)

    # ---------- Luftqualität ----------
    with c2:

        fig, ax = plt.subplots(figsize=(8, 4))

        ax.fill_between(
            df["Datum"],
            df["Feinstaub (µg/m³)"],
            alpha=0.4
        )

        ax.plot(
            df["Datum"],
            df["Feinstaub (µg/m³)"]
        )

        ax.set_title("Luftqualität")
        ax.set_ylabel("Feinstaub")

        plt.xticks(rotation=45)

        st.pyplot(fig)

        # ---------- Energie ----------
        fig, ax = plt.subplots(figsize=(8, 4))

        sns.lineplot(
            data=df,
            x="Datum",
            y="Energie (MWh)",
            ax=ax
        )

        ax.set_title("Energieverbrauch")

        plt.xticks(rotation=45)

        st.pyplot(fig)