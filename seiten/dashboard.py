# pages/dashboard.py

import streamlit as st
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from pathlib import Path

from utils.components import stat


# =====================================================
# CSV-DATEN LADEN
# =====================================================
def load_measurements():

    # -------------------------------------------------
    # Projektpfad
    # -------------------------------------------------
    current_dir = Path(__file__).resolve().parent

    # pages -> Hauptprojektordner
    project_root = current_dir.parent

    # -------------------------------------------------
    # CSV-Datei
    # -------------------------------------------------
    csv_path = project_root / "data" / "csv" / "messwerte.csv"

    # -------------------------------------------------
    # Debug-Ausgabe
    # -------------------------------------------------
    st.write("CSV-Datei:", csv_path)

    # -------------------------------------------------
    # Existiert Datei?
    # -------------------------------------------------
    if not csv_path.exists():

        st.error(
            f"CSV-Datei nicht gefunden:\n{csv_path}"
        )

        return pd.DataFrame()

    # -------------------------------------------------
    # CSV laden
    # -------------------------------------------------
    try:

        df = pd.read_csv(
            csv_path,
            sep=";"
        )

        return df

    except Exception as e:

        st.error(
            f"Fehler beim Laden der CSV-Datei: {e}"
        )

        return pd.DataFrame()


# =====================================================
# DASHBOARD
# =====================================================
def show():

    st.title("📊 Live-Dashboard")

    st.caption(
        "Analyse von Umwelt- und Smart-City-Messwerten"
    )

    # -------------------------------------------------
    # Daten laden
    # -------------------------------------------------
    df = load_measurements()

    # -------------------------------------------------
    # Falls keine Daten
    # -------------------------------------------------
    if df.empty:
        return

    # -------------------------------------------------
    # Statistik-Karten
    # -------------------------------------------------
    st.subheader("Messwerte Übersicht")

    c1, c2, c3 = st.columns(3)

    with c1:

        stat(
            f"{df['Feinstaub (PM10)'].mean():.1f}",
            "Ø Feinstaub"
        )

    with c2:

        stat(
            f"{df['Stickstoffdioxid'].mean():.1f}",
            "Ø Stickstoffdioxid"
        )

    with c3:

        stat(
            f"{df['Kohlenmonoxid'].mean():.2f}",
            "Ø Kohlenmonoxid"
        )

    c4, c5, c6 = st.columns(3)

    with c4:

        stat(
            f"{df['Temperatur'].mean():.1f} °C",
            "Ø Temperatur"
        )

    with c5:

        stat(
            f"{df['Luftdruck'].mean():.0f} hPa",
            "Ø Luftdruck"
        )

    with c6:

        stat(
            f"{df['Regendauer'].mean():.1f} h",
            "Ø Regendauer"
        )

    # -------------------------------------------------
    # Datenauswahl
    # -------------------------------------------------
    st.subheader("Diagramm-Auswahl")

    quelle = st.multiselect(
        "Messwerte auswählen",
        [
            "Feinstaub (PM10)",
            "Stickstoffdioxid",
            "Kohlenmonoxid",
            "Temperatur",
            "Luftdruck",
            "Regendauer"
        ],
        default=[
            "Temperatur",
            "Feinstaub (PM10)"
        ]
    )

    # -------------------------------------------------
    # Diagramm
    # -------------------------------------------------
    if quelle:

        fig, ax = plt.subplots(
            figsize=(12, 5)
        )

        for q in quelle:

            sns.lineplot(
                data=df,
                y=q,
                label=q,
                ax=ax
            )

        ax.set_title(
            "Umwelt- und Smart-City-Messwerte"
        )

        ax.set_xlabel("Messpunkte")

        ax.set_ylabel("Werte")

        plt.xticks(rotation=45)

        st.pyplot(fig)

    # -------------------------------------------------
    # Korrelationsmatrix
    # -------------------------------------------------
    st.subheader("Korrelationsmatrix")

    fig, ax = plt.subplots(
        figsize=(8, 6)
    )

    sns.heatmap(
        df.corr(numeric_only=True),
        annot=True,
        cmap="crest",
        linewidths=0.5,
        ax=ax
    )

    ax.set_title(
        "Zusammenhänge der Messwerte"
    )

    st.pyplot(fig)

    # -------------------------------------------------
    # Rohdaten
    # -------------------------------------------------
    with st.expander("Messwerte anzeigen"):

        st.dataframe(
            df,
            width="stretch"
        )