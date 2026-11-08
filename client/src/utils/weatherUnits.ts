export const unitSystems = ["si", "imperial"] as const;
export type UnitSystem = (typeof unitSystems)[number];

type UnitDefinition = {
  label: string;
  temperature: string;
  precipitation: string;
};

export type WeatherUnitConfig = {
  units: Record<UnitSystem, UnitDefinition>;
  decimals: number;
};

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
