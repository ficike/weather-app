import type { ReactNode } from "react";
import { UnitSystemProvider } from "../contexts/UnitSystemContext.tsx";
import { WeatherOptionsProvider } from "../contexts/WeatherOptionsContext.tsx";

type AppProvidersProps = {
  children: ReactNode;
};

export function AppProviders({ children }: AppProvidersProps) {
  return (
    <WeatherOptionsProvider>
      <UnitSystemProvider>{children}</UnitSystemProvider>
    </WeatherOptionsProvider>
  );
}
