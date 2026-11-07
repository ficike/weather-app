type NavigationProps = {
  currentPath: string;
  onNavigate: (path: string) => void;
};

const links = [
  { path: "/city-weather", label: "Város időjárása" },
  { path: "/region-weather", label: "Régió időjárása" },
];

export function Navigation({ currentPath, onNavigate }: NavigationProps) {
  return (
    <nav aria-label="Felületválasztó" className="px-3 py-3">
      <ul className="flex max-w-4xl gap-3">
        {links.map(({ path, label }) => (
          <li key={path}>
            <a
              href={path}
              aria-current={currentPath === path ? "page" : undefined}
              onClick={(event) => {
                if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
                event.preventDefault();
                onNavigate(path);
              }}
              className={`inline-block rounded-lg px-4 py-2 font-bold ${
                currentPath === path
                  ? "bg-amber-400"
                  : "bg-amber-100 hover:bg-amber-200"
              }`}
            >
              {label}
            </a>
          </li>
        ))}
      </ul>
    </nav>
  );
}
