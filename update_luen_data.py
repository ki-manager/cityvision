import os
import json
import pandas as pd
import requests

from datetime import datetime


# =====================================================
# Konfiguration
# =====================================================

URL = "https://www.luen-ni.de/json30m.txt"

OUTPUT_DIR = "data/stations"

LOG_FILE = "data/import.log"


# =====================================================
# Ordner erstellen
# =====================================================

os.makedirs(

    OUTPUT_DIR,

    exist_ok=True

)

os.makedirs(

    "data",

    exist_ok=True

)


# =====================================================
# Logging
# =====================================================

def log(message):

    timestamp = datetime.now().strftime(
        "%Y-%m-%d %H:%M:%S"
    )

    text = f"[{timestamp}] {message}"

    print(text)

    with open(

        LOG_FILE,

        "a",

        encoding="utf-8"

    ) as file:

        file.write(text + "\n")


# =====================================================
# Datenbereinigung
# =====================================================

def clean_value(value):

    try:

        value = str(value)

        value = value.replace(",", ".")
        value = value.replace("<", "")
        value = value.replace(">", "")
        value = value.replace(" ", "")

        if value == "":
            return None

        value = float(value)

        if value < -999:
            return None

        if value > 999999:
            return None

        return value

    except:

        return None


# =====================================================
# Ausreißer entfernen
# =====================================================

def remove_outliers(df):

    if len(df) < 5:

        return df


    mean = df["Messwert"].mean()

    std = df["Messwert"].std()


    if pd.isna(std) or std == 0:

        return df


    lower = mean - 3 * std

    upper = mean + 3 * std


    df = df[

        (df["Messwert"] >= lower)

        &

        (df["Messwert"] <= upper)

    ]


    return df


# =====================================================
# Daten herunterladen
# =====================================================

log("Starte Datenimport")


response = requests.get(

    URL,

    timeout=60

)


data = response.json()


log("Daten erfolgreich geladen")


# =====================================================
# Struktur prüfen
# =====================================================

if "messwerte" not in data:

    log("FEHLER: Keine Messwerte gefunden")

    exit()


log(

    f"Stationen gefunden: "

    f"{len(data['messwerte'])}"

)


# =====================================================
# Metadaten
# =====================================================

metadata_rows = []


# =====================================================
# Stationen verarbeiten
# =====================================================

for station in data["messwerte"]:

    try:

        # =============================================
        # Stationscode
        # =============================================

        station_code = station.get(

            "kennung",

            "UNKNOWN"

        )


        log(

            f"Verarbeite Station: "

            f"{station_code}"

        )


        # =============================================
        # Alle Daten sammeln
        # =============================================

        station_rows = []


        # =============================================
        # Messstellen
        # =============================================

        messstellen = station.get(

            "messstellen",

            []

        )


        log(

            f"Messstellen: "

            f"{len(messstellen)}"

        )


        # =============================================
        # Komponenten verarbeiten
        # =============================================

        for messstelle in messstellen:

            component = messstelle.get(

                "kennung",

                "UNKNOWN"

            )


            values = messstelle.get(

                "verlauf_stundenwerte",

                []

            )


            log(

                f"Komponente: "

                f"{component}"

            )


            # =========================================
            # Werte erzeugen
            # =========================================

            for index, value in enumerate(values):

                clean = clean_value(value)


                station_rows.append({

                    "Zeitindex":
                        index,

                    "Station":
                        station_code,

                    "Komponente":
                        component,

                    "Messwert":
                        clean

                })


        # =============================================
        # DataFrame
        # =============================================

        df = pd.DataFrame(
            station_rows
        )


        # =============================================
        # Prüfen
        # =============================================

        if df.empty:

            log(
                f"Keine Daten "
                f"für {station_code}"
            )

            continue


        # =============================================
        # Statistik vor Bereinigung
        # =============================================

        before_count = len(df)


        # =============================================
        # Fehlende Werte entfernen
        # =============================================

        df = df.dropna(
            subset=["Messwert"]
        )


        # =============================================
        # Doppelte entfernen
        # =============================================

        df = df.drop_duplicates()


        # =============================================
        # Datentypen korrigieren
        # =============================================

        df["Messwert"] = pd.to_numeric(

            df["Messwert"],

            errors="coerce"

        )


        # =============================================
        # Ausreißer entfernen
        # =============================================

        cleaned_groups = []


        grouped = df.groupby(
            "Komponente"
        )


        for component, component_df in grouped:

            component_df = remove_outliers(
                component_df
            )

            cleaned_groups.append(
                component_df
            )


        df = pd.concat(
            cleaned_groups
        )


        # =============================================
        # Sortieren
        # =============================================

        df = df.sort_values(

            by=[

                "Komponente",

                "Zeitindex"

            ]

        )


        # =============================================
        # Statistik nach Bereinigung
        # =============================================

        after_count = len(df)


        removed = (
            before_count - after_count
        )


        log(

            f"Bereinigt: "

            f"{removed} Werte entfernt"

        )


        # =============================================
        # CSV speichern
        # =============================================

        csv_path = os.path.join(

            OUTPUT_DIR,

            f"{station_code}.csv"

        )


        df.to_csv(

            csv_path,

            index=False,

            encoding="utf-8"

        )


        log(

            f"CSV gespeichert: "

            f"{csv_path}"

        )


        # =============================================
        # Metadaten
        # =============================================

        metadata_rows.append({

            "Station":
                station_code,

            "Datensätze":
                len(df),

            "Komponenten":
                df["Komponente"]
                .nunique(),

            "Minimum":
                round(
                    df["Messwert"].min(),
                    2
                ),

            "Maximum":
                round(
                    df["Messwert"].max(),
                    2
                ),

            "Mittelwert":
                round(
                    df["Messwert"].mean(),
                    2
                )

        })


    except Exception as error:

        log(

            f"FEHLER bei Station "

            f"{station_code}: "

            f"{error}"

        )


# =====================================================
# Metadaten speichern
# =====================================================

metadata_df = pd.DataFrame(
    metadata_rows
)


metadata_path = os.path.join(

    OUTPUT_DIR,

    "metadata.csv"

)


metadata_df.to_csv(

    metadata_path,

    index=False,

    encoding="utf-8"

)


log(

    f"Metadaten gespeichert: "

    f"{metadata_path}"

)


# =====================================================
# Letztes Update
# =====================================================

with open(

    "data/latest_update.txt",

    "w",

    encoding="utf-8"

) as file:

    file.write(

        datetime.now().strftime(
            "%Y-%m-%d %H:%M:%S"
        )

    )


# =====================================================
# Fertig
# =====================================================

log("Import abgeschlossen")