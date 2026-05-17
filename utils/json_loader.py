# utils/json_loader.py

import streamlit as st
import pandas as pd
from pathlib import Path
import json


def load_json_data():

    # ---------------------------------------------------
    # Projektpfad
    # ---------------------------------------------------
    current_dir = Path(__file__).resolve().parent

    # utils -> Projektordner
    project_root = current_dir.parent

    # ---------------------------------------------------
    # JSON-Ordner
    # ---------------------------------------------------
    data_path = project_root / "data" / "json"

    # ---------------------------------------------------
    # JSON-Dateien suchen
    # ---------------------------------------------------
    json_files = list(data_path.glob("*.json"))

    if not json_files:
        st.error(f"Keine JSON-Dateien gefunden: {data_path}")
        return pd.DataFrame()

    # ---------------------------------------------------
    # Auswahlmenü
    # ---------------------------------------------------
    selected_files = st.multiselect(
        "JSON-Dateien auswählen",
        options=[file.name for file in json_files],
        default=[file.name for file in json_files]
    )

    if not selected_files:
        st.warning("Keine Datei ausgewählt.")
        return pd.DataFrame()

    # ---------------------------------------------------
    # Daten laden
    # ---------------------------------------------------
    all_data = []

    for filename in selected_files:

        file_path = data_path / filename

        try:

            with open(file_path, "r", encoding="utf-8") as f:

                data = json.load(f)

                # Liste
                if isinstance(data, list):
                    all_data.extend(data)

                # Einzelobjekt
                elif isinstance(data, dict):
                    all_data.append(data)

        except Exception as e:
            st.error(f"Fehler bei {filename}: {e}")

    # ---------------------------------------------------
    # DataFrame
    # ---------------------------------------------------
    df = pd.DataFrame(all_data)

    return df