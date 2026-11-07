type SearchButtonProps = {
  loading: boolean;
  disabled?: boolean;
  onClick: () => void;
};

export function SearchButton({ loading, disabled = false, onClick }: SearchButtonProps) {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={loading || disabled}
      className="mt-4 rounded-lg bg-amber-300 px-4 py-2 font-bold hover:bg-amber-400 active:bg-amber-500 disabled:cursor-not-allowed disabled:opacity-50"
    >
      {loading ? "Keresés…" : "Keresés"}
    </button>
  );
}
