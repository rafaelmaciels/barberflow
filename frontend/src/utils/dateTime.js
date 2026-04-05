const BRAZIL_TIME_ZONE = "America/Sao_Paulo";

function getDatePartsInBrazil(date = new Date()) {
  const formatter = new Intl.DateTimeFormat("en-CA", {
    timeZone: BRAZIL_TIME_ZONE,
    year: "numeric",
    month: "2-digit",
    day: "2-digit"
  });

  const parts = formatter.formatToParts(date);
  const year = parts.find((part) => part.type === "year")?.value;
  const month = parts.find((part) => part.type === "month")?.value;
  const day = parts.find((part) => part.type === "day")?.value;

  return { year, month, day };
}

export function getBrazilISODate(date = new Date()) {
  const { year, month, day } = getDatePartsInBrazil(date);
  return `${year}-${month}-${day}`;
}

export function formatBrazilWeekdayAndDate(isoDate) {
  const [year, month, day] = isoDate.split("-").map(Number);
  const utcDate = new Date(Date.UTC(year, month - 1, day, 12, 0, 0));

  const weekday = new Intl.DateTimeFormat("pt-BR", {
    timeZone: BRAZIL_TIME_ZONE,
    weekday: "long"
  }).format(utcDate);

  const formattedDate = new Intl.DateTimeFormat("pt-BR", {
    timeZone: BRAZIL_TIME_ZONE
  }).format(utcDate);

  return `${weekday.charAt(0).toUpperCase()}${weekday.slice(1)} ${formattedDate}`;
}

export function isBrazilSunday(isoDate) {
  const [year, month, day] = isoDate.split("-").map(Number);
  const utcDate = new Date(Date.UTC(year, month - 1, day, 12, 0, 0));

  return utcDate.getUTCDay() === 0;
}

export { BRAZIL_TIME_ZONE };
