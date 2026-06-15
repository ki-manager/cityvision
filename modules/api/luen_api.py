
import requests
import pandas as pd
from modules.api.station_mapping import station_mapping

class LuenAPI:

    URL = "https://www.luen-ni.de/json30m.txt"

    def download_data(self):
        response = requests.get(self.URL, timeout=30)
        return response.json()

    def create_dataframe(self, data):

        rows = []

        for station in data["messwerte"]:

            station_code = station["kennung"]

            mapping = station_mapping.get(
                station_code,
                {
                    "name": station_code,
                    "typ": "Unbekannt",
                    "region": "Unbekannt"
                }
            )

            for messstelle in station["messstellen"]:

                component = messstelle["kennung"]

                values = messstelle["verlauf_stundenwerte"]

                for value in values:

                    try:
                        clean_value = (
                            str(value)
                            .replace(",", ".")
                            .replace("<", "")
                        )

                        clean_value = float(clean_value)

                        rows.append({
                            "Stationscode": station_code,
                            "Station": mapping["name"],
                            "Typ": mapping["typ"],
                            "Region": mapping["region"],
                            "Komponente": component,
                            "Messwert": clean_value
                        })

                    except:
                        pass

        return pd.DataFrame(rows)
