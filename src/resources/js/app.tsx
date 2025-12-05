import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { route as ziggyRoute } from 'ziggy-js';
import '../css/app.css';

declare global {
	interface Window {
		route: typeof ziggyRoute;
	}
}
window.route = route;

const pages = import.meta.glob('./Pages/**/*.tsx');

createInertiaApp({
	resolve: (name) => {
		const page = pages[`./Pages/${name}.tsx`];

		if (!page) {
			throw new Error(`Page ${name} not found in ./Pages/`);
		}

		return page().then((module) => {
			return module.default;
		});
	},
	setup({ el, App, props }) {
		const root = createRoot(el as HTMLElement);
		root.render(<App {...props} />);
	},
});
