// DateRangePicker.tsx
import React from 'react';
import ReactDatePicker from 'react-datepicker';

type Props = {
	filterDateStart: string;
	filterDateEnd: string;
	setFilterDateStart: (v: string) => void;
	setFilterDateEnd: (v: string) => void;
};

const DateRangePicker: React.FC<Props> = ({ filterDateStart, filterDateEnd, setFilterDateStart, setFilterDateEnd }) => {
	// convert string "YYYY-MM-DD" -> Date
	const startDate = filterDateStart ? new Date(filterDateStart) : null;
	const endDate = filterDateEnd ? new Date(filterDateEnd) : null;

	const handleChange = (dates: [Date | null, Date | null]) => {
		const [start, end] = dates;

		if (start) {
			setFilterDateStart(start.toISOString().slice(0, 10)); // "YYYY-MM-DD"
		} else {
			setFilterDateStart('');
		}

		if (end) {
			setFilterDateEnd(end.toISOString().slice(0, 10));
		} else {
			setFilterDateEnd('');
		}
	};

	return (
		<div className="w-full">
			<label className="block text-sm font-medium text-gray-700 mb-1">日付範囲</label>

			<div className="border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-white">
				<ReactDatePicker
					selectsRange
					startDate={startDate}
					endDate={endDate}
					onChange={handleChange}
					dateFormat="yyyy/MM/dd"
					placeholderText="YYYY/MM/DD - YYYY/MM/DD"
					monthsShown={2}
					className="w-full bg-transparent border-none focus:outline-none text-xs placeholder:text-xs"
				/>
			</div>
		</div>
	);
};

export default DateRangePicker;
