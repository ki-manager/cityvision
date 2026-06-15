class DataCleaner:

    def clean_dataframe(self, df):

        df = df.dropna()
        df = df.drop_duplicates()

        return df
