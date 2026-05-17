import streamlit as st
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from pathlib import Path
import json


def load_json_data():

    # Pfad zum JSON-Verzeichnis
    data_path = Path("data/json")

    json_files = list(data_path.glob("*.json"))

    if not json_files:
        st.error("Keine JSON-Dateien im Verzeichnis data/json gefunden.")
        return pd.DataFrame()

    all_data = []

    # Alle JSON-Dateien einlesen
    for file in json_files:

        with open(file, "r", encoding="utf-8") as f:
            data = json.load(f)

            # Falls Liste von Datensätzen
            if isinstance(data, list):
                all_data.extend(data)

            # Falls einzelnes Objekt
            elif isinstance(data, dict):
                all_data.append(data)

    # DataFrame erzeugen
    df = pd.DataFrame(all_data)

    return df


def show():

    st.title("Visualisierung")
    st.caption("Beispielhafte Diagramme moderner Dashboards")

    # ---------- JSON-Daten laden ----------
    df = load_json_data()

    # Falls keine Daten vorhanden
    if df.empty:
        return

    # ---------- Datumsformat ----------
    df["Datum"] = pd.to_datetime(df["Datum"])

    c1, c2 = st.columns(2)

    # ====================================================
    # Wetter
    # ====================================================
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

        # ------------------------------------------------
        # Verkehr
        # ------------------------------------------------
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

    # ====================================================
    # Luftqualität
    # ====================================================
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

        # ------------------------------------------------
        # Energie
        # ------------------------------------------------
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

    # ====================================================
    # Rohdaten anzeigen
    # ====================================================
    with st.expander("JSON-Rohdaten anzeigen"):
        st.dataframe(df, width="stretch")