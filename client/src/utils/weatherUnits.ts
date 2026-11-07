export type WeatherUnitConfig = {
  units: Record<string, { label: string; temperature: string; precipitation: string }>;
  decimals: number;
};

export type UnitSystem = keyof WeatherUnitConfig["units"];

export function convertWeatherValue(key: string, value: number, unitSystem: UnitSystem): number {
  if (unitSystem === "imperial") {
    if (isTemperature(key)) return (value * 9) / 5 + 32
    if (key === "precipitation") return value / 25.4;
  }
  return value;
}

export function formatWeatherValue(
  key: string,
  value: number,
  unitSystem: UnitSystem,
  config: WeatherUnitConfig,
): string {
  const convertedValue = convertWeatherValue(key, value, unitSystem);
  const unit = isTemperature(key)
    ? config.units[unitSystem].temperature
    : config.units[unitSystem].precipitation;
  return `${convertedValue.toFixed(config.decimals)} ${unit}`;
}

function isTemperature(key: string) {
  return key === "min_temp" || key === "max_temp";
}
