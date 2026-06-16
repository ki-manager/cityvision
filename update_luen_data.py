import os
import json
import pandas as pd
import requests


# =====================================================
# Einstellungen
# =====================================================

URL = "https://www.luen-ni.de/json30m.txt"

OUTPUT_DIR = "cityvision/data/stations"


# =====================================================
# Ordner erstellen
# =====================================================

os.makedirs(
    OUTPUT_DIR,
    exist_ok=True
)


# =====================================================
# Daten laden
# =====================================================

print("Lade Daten...")

response = requests.get(
    URL,
    timeout=60
)

data = response.json()

print("Daten erfolgreich geladen")


# =====================================================
# Struktur prüfen
# =====================================================

if "messwerte" not in data:

    print("Fehler:")
    print("Keine 'messwerte' gefunden")

    exit()


print(
    f"Anzahl Stationen: "
    f"{len(data['messwerte'])}"
)


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

        print(
            f"\nVerarbeite Station: "
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
        # JSON speichern
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


        print(
            f"JSON gespeichert:"
            f" {json_path}"
        )


        # =============================================
        # Messstellen prüfen
        # =============================================

        messstellen = station.get(
            "messstellen",
            []
        )

        print(
            f"Messstellen:"
            f" {len(messstellen)}"
        )


        # =============================================
        # Komponenten verarbeiten
        # =============================================

        for messstelle in messstellen:

            component = messstelle.get(
                "kennung",
                "UNKNOWN"
            )

            print(
                f"  Komponente:"
                f" {component}"
            )


            # =========================================
            # Werte laden
            # =========================================

            values = messstelle.get(
                "verlauf_stundenwerte",
                []
            )


            # =========================================
            # Zeilen erzeugen
            # =========================================

            rows = []

            for index, value in enumerate(values):

                try:

                    clean_value = (
                        str(value)
                        .replace(",", ".")
                        .replace("<", "")
                    )

                    clean_value = float(
                        clean_value
                    )

                except:

                    clean_value = None


                rows.append({

                    "Zeitindex":
                        index,

                    "Station":
                        station_code,

                    "Komponente":
                        component,

                    "Messwert":
                        clean_value

                })


            # =========================================
            # Prüfen ob Daten existieren
            # =========================================

            if len(rows) == 0:

                print(
                    f"  Keine Werte "
                    f"für {component}"
                )

                continue


            # =========================================
            # DataFrame
            # =========================================

            df = pd.DataFrame(rows)


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


            print(
                f"  CSV gespeichert:"
                f" {csv_path}"
            )


    except Exception as error:

        print(
            f"Fehler bei Station:"
            f" {error}"
        )


# =====================================================
# Fertig
# =====================================================

print("\nImport abgeschlossen")