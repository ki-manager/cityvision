
import streamlit as st
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import requests

from pathlib import Path
from bs4 import BeautifulSoup
from urllib.parse import urljoin
from datetime import datetime, timedelta


BASE_URL = "https://www.umwelt.niedersachsen.de/luft/LUEN/aktuelle_messwerte/archiv/download/"
CACHE_MAX_AGE_HOURS = 24


def get_csv_url():

    try:

        response = requests.get(BASE_URL, timeout=20)

        soup = BeautifulSoup(
            response.text,
            "html.parser"
        )

        csv_links = []

        for a in soup.find_all("a", href=True):

            href = a["href"]

            if ".csv" in href.lower():

                full_url = urljoin(
                    BASE_URL,
                    href
                )

                csv_links.append(full_url)

        if not csv_links:

            st.error(
                "Keine CSV-Downloadlinks gefunden."
            )

            return None

        # Erste CSV verwenden
        return csv_links[0]

    except Exception as e:

        st.error(
            f"Fehler beim Ermitteln der CSV-URL:\n{e}"
        )

        return None


def is_cache_valid(csv_path):

    if not csv_path.exists():
        return False

    modified = datetime.fromtimestamp(
        csv_path.stat().st_mtime
    )

    age = datetime.now() - modified

    return age < timedelta(
        hours=CACHE_MAX_AGE_HOURS
    )


def download_csv(csv_url, csv_path):

    try:

        response = requests.get(
            csv_url,
            timeout=30
        )

        response.raise_for_status()

        csv_path.parent.mkdir(
            parents=True,
            exist_ok=True
        )

        with open(csv_path, "wb") as f:
            f.write(response.content)

        st.success(
            "CSV-Datei aktualisiert."
        )

    except Exception as e:

        st.error(
            f"Fehler beim Download:\n{e}"
        )


def load_measurements():

    current_dir = Path(__file__).resolve().parent
    project_root = current_dir.parent

    csv_path = (
        project_root
        / "data"
        / "csv"
        / "messwerte.csv"
    )

    st.write("CSV-Datei:", csv_path)

    # -----------------------------------
    # Schritt 1:
    # CSV-URL automatisch ermitteln
    # -----------------------------------

    csv_url = get_csv_url()

    if not csv_url:
        return pd.DataFrame()

    st.write("Gefundene CSV-URL:", csv_url)

    # -----------------------------------
    # Schritt 2:
    # Cache prüfen
    # -----------------------------------

    if not is_cache_valid(csv_path):

        st.info(
            "Cache abgelaufen oder Datei fehlt. "
            "CSV wird neu geladen..."
        )

        # -----------------------------------
        # Schritt 3:
        # CSV herunterladen
        # -----------------------------------

        download_csv(
            csv_url,
            csv_path
        )

    else:

        st.success(
            "Lokale Cache-Datei wird verwendet."
        )

    if not csv_path.exists():

        st.error(
            f"Datei nicht gefunden:\n{csv_path}"
        )

        return pd.DataFrame()

    try:

        df = pd.read_csv(
            csv_path,
            sep=";",
            encoding="latin1",
            skiprows=2,
            decimal=","
        )

        df = df.dropna(
            axis=1,
            how="all"
        )

        df.columns = [
            "Datum",
            "Feinstaub (PM10)",
            "Temperatur",
            "Luftdruck",
            "Regendauer"
        ]

        df = df[
            [
                "Datum",
                "Feinstaub (PM10)",
                "Temperatur",
                "Luftdruck",
                "Regendauer"
            ]
        ]

        df["Datum"] = pd.to_datetime(
            df["Datum"],
            format="%d.%m.%Y"
        )

        numeric_cols = [
            "Feinstaub (PM10)",
            "Temperatur",
            "Luftdruck",
            "Regendauer"
        ]

        for col in numeric_cols:

            df[col] = pd.to_numeric(
                df[col],
                errors="coerce"
            )

        return df

    except Exception as e:

        st.error(
            f"Fehler beim Laden:\n{e}"
        )

        return pd.DataFrame()


def show():

    st.title("Live-Dashboard")

    st.text(
        "Analyse von Umweltmesswerten aus Braunschweig"
    )

    st.text(
        "Quelle: https://www.umwelt.niedersachsen.de/luft/LUEN/aktuelle_messwerte/archiv/download/"
    )

    df = load_measurements()

    if df.empty:
        return

    with st.expander("Geladene Messwerte"):

        st.dataframe(
            df,
            width="stretch"
        )

    st.subheader("Messwerte Übersicht")

    c1, c2, c3, c4 = st.columns(4)

    with c1:

        stat(
            f"{df['Feinstaub (PM10)'].mean():.1f}",
            "Ø Feinstaub (PM10)"
        )

    with c2:

        stat(
            f"{df['Temperatur'].mean():.1f} °C",
            "Ø Temperatur"
        )

    with c3:

        stat(
            f"{df['Luftdruck'].mean():.0f} hPa",
            "Ø Luftdruck"
        )

    with c4:

        stat(
            f"{df['Regendauer'].mean():.0f} min",
            "Ø Regendauer"
        )

    st.subheader("Diagramm")

    quelle = st.multiselect(
        "Messwerte auswählen",
        [
            "Feinstaub (PM10)",
            "Temperatur",
            "Luftdruck",
            "Regendauer"
        ],
        default=[
            "Feinstaub (PM10)",
            "Temperatur",
            "Luftdruck",
            "Regendauer"
        ]
    )

    if quelle:

        fig, ax = plt.subplots(
            figsize=(12, 5)
        )

        for q in quelle:

            sns.lineplot(
                data=df,
                x="Datum",
                y=q,
                label=q,
                ax=ax
            )

        ax.set_title(
            "Umweltmesswerte"
        )

        ax.set_xlabel("Datum")

        ax.set_ylabel("Messwerte")

        plt.xticks(rotation=45)

        st.pyplot(fig)
