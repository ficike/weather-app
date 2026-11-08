export type WeatherRow = {
  date: string;
  min_temp: number | null;
  max_temp: number | null;
  precipitation: number | null;
};

export type WeatherStatistics = Partial<Record<"min_temp" | "max_temp" | "precipitation", {
  median: number | null;
  mode: number[];
}>>;

export type WeatherResponse = {
  header: Record<string, string>;
  data: WeatherRow[];
  statistics: WeatherStatistics;
};
