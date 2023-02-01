import BootstrapVue from 'bootstrap-vue/dist/bootstrap-vue.esm';
import IconsPlugin from 'bootstrap-vue/dist/bootstrap-vue.esm';
import { Store } from "vuex";
import * as compat from '@vue/compat';

declare module '@vue/runtime-core' {
	interface ComponentCustomProperties {
		$BootstrapVue: BootstrapVue;
		$IconsPlugin: IconsPlugin;
		$store: Store<State>;
		$compat: compat;
	}
}
