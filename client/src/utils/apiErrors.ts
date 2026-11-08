export async function throwIfResponseFailed(response: Response): Promise<void> {
  if (response.ok) return;

  const responseDetails = await response.text();
  const details = responseDetails ? `: ${responseDetails}` : "";
  throw new Error(`API hiba (${response.status})${details}`);
}

export function logApiError(error: unknown): void {
  if (import.meta.env.DEV) {
    console.error("API kérés sikertelen:", error);
  }
}
