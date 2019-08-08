export default function DayHeaderWrapper({
  className,
  columns,
  style,
}) {
  return (
    <div className={className} role="row" style={style}>
      {columns}
    </div>
  );
}
