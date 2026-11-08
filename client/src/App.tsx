import type { ReactNode } from "react";
import { Navigation } from "./components/Navigation.tsx";
import { Route, Routes } from "./routing/Routes.tsx";
import { CityWeather } from "./pages/CityWeather.tsx";
import { NotFound } from "./pages/NotFound.tsx";
import { RegionWeather } from "./pages/RegionWeather.tsx";
import { useCurrentPath } from "./routing/useCurrentPath.ts";
import { appRoutes, type PageComponent } from "./routing/routes.ts";

export function App() {
  const { currentPath, navigate } = useCurrentPath();
  const pageElements: Record<PageComponent, ReactNode> = {
    CityWeather: <CityWeather />,
    RegionWeather: <RegionWeather />,
  };

  return (
    <>
      <Navigation currentPath={currentPath} onNavigate={navigate} />
      <Routes path={currentPath} notFound={<NotFound onNavigateHome={() => navigate("/city-weather")} />}>
        {appRoutes.map(({ path, component }) => (
          <Route key={path} path={path} element={pageElements[component]} />
        ))}
      </Routes>
    </>
  );
}
