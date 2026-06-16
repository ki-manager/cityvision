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

        # =============================================
        # In String umwandeln
        # =============================================

        value = str(value)


        # =============================================
        # Sonderzeichen entfernen
        # =============================================

        value = value.replace(",", ".")
        value = value.replace("<", "")
        value = value.replace(">", "")
        value = value.replace(" ", "")


        # =============================================
        # Leere Werte
        # =============================================

        if value == "":
            return None


        # =============================================
        # Umwandlung
        # =============================================

        value = float(value)


        # =============================================
        # Unrealistische Werte filtern
        # =============================================

        if value < -999:
            return None

        if value > 999999:
            return None


        return value

    except:

        return None


# =====================================================
# Ausreißer erkennen
# =====================================================

def remove_outliers(df):

    if len(df) < 5:
        return df

    mean = df["Messwert"].mean()

    std = df["Messwert"].std()

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
# Metadaten sammeln
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
        # Stationsordner
        # =============================================

        station_dir = os.path.join(
            OUTPUT_DIR,
            station_code
        )

        os.makedirs(
            station_dir,
            exist_ok=True
        )


        # =============================================
        # Rohdaten speichern
        # =============================================

        json_path = os.path.join(

            station_dir,

            f"{station_code}.json"

        )

        with open(

            json_path,
            "w",
            encoding="utf-8"

        ) as file:

            json.dump(

                station,
                file,
                ensure_ascii=False,
                indent=4

            )


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
        # Komponenten
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
            # Zeilen erzeugen
            # =========================================

            rows = []

            for index, value in enumerate(values):

                clean = clean_value(value)

                rows.append({

                    "Zeitindex":
                        index,

                    "Station":
                        station_code,

                    "Komponente":
                        component,

                    "Messwert":
                        clean

                })


            # =========================================
            # DataFrame
            # =========================================

            df = pd.DataFrame(rows)


            # =========================================
            # Bereinigung
            # =========================================

            before_count = len(df)

            # Nullwerte entfernen
            df = df.dropna(
                subset=["Messwert"]
            )

            # Doppelte entfernen
            df = df.drop_duplicates()

            # Ausreißer entfernen
            df = remove_outliers(df)

            after_count = len(df)


            # =========================================
            # Statistik
            # =========================================

            removed = (
                before_count - after_count
            )

            log(
                f"Bereinigt: "
                f"{removed} Werte entfernt"
            )


            # =========================================
            # Prüfen ob Daten vorhanden
            # =========================================

            if df.empty:

                log(
                    f"Keine gültigen Daten "
                    f"für {component}"
                )

                continue


            # =========================================
            # CSV speichern
            # =========================================

            csv_path = os.path.join(

                station_dir,

                f"{station_code}_{component}.csv"

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


            # =========================================
            # Metadaten
            # =========================================

            metadata_rows.append({

                "Station":
                    station_code,

                "Komponente":
                    component,

                "Datensätze":
                    len(df),

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
# Letztes Update speichern
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