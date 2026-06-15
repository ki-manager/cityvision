class EnvironmentClassifier:

    def classify_temperature(self, value):

        if value < 5:
            return "Kalt"
        elif value < 20:
            return "Warm"
        elif value < 30:
            return "Heiß"
        else:
            return "Extreme Hitze"
