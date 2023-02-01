import BootstrapVue from 'bootstrap-vue/dist/bootstrap-vue.esm';

declare module '@vue/runtime-core' {
	interface ComponentCustomProperties {
		$BootstrapVue: BootstrapVue;
	}
}