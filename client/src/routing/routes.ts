export const appRoutes = [
  { path: "/city-weather", label: "Város időjárása", component: "CityWeather" },
  { path: "/region-weather", label: "Régió időjárása", component: "RegionWeather" },
] as const;

export type AppPath = (typeof appRoutes)[number]["path"];
export type PageComponent = (typeof appRoutes)[number]["component"];
export const defaultAppPath: AppPath = "/city-weather";

export function resolveAppPath(pathname: string): string {
  return pathname === "/" ? defaultAppPath : pathname;
}
