<template>
	<div id="ovhai_prefs" class="section">
		<h2>
			<OvhIcon class="icon" />
			{{ t('integration_ovhai', 'OVH AI integration') }}
		</h2>
		<div id="ovh-content">
			<div class="line">
				<NcTextField
					class="input"
					:value.sync="state.api_key"
					:label="t('integration_ovhai', 'OVH API token')"
					:placeholder="t('integration_ovhai', 'Your API token')"
					:show-trailing-button="!!state.api_key"
					@update:value="onInput"
					@trailing-button-click="state.api_key = '' ; onInput()" />
			</div>
			<p class="settings-hint">
				<OpenInNewIcon :size="20" class="icon" />
				<a href="https://ovh.com" target="_blank" class="external">
					{{ t('integration_ovhai', 'Get your API token on ovh.com') }}
				</a>
			</p>
			<h3>
				{{ t('integration_ovhai', 'Text generation') }}
			</h3>
			<div class="line">
				<NcTextField
					class="input"
					:value.sync="state.llm_model_name"
					:label="t('integration_ovhai', 'Text generation model name')"
					:show-trailing-button="!!state.llm_model_name"
					@update:value="onInput"
					@trailing-button-click="state.llm_model_name = '' ; onInput()" />
				<NcButton type="tertiary"
					:title="t('integration_ovhai', 'You can find model names on the OVH website. For example: \'/mistralai/Mistral-7B-Instruct-v0.1\'')">
					<template #icon>
						<HelpCircleIcon />
					</template>
				</NcButton>
			</div>
			<div class="line">
				<NcTextField
					class="input"
					:value.sync="state.llm_url_prefix"
					:label="t('integration_ovhai', 'Text generation endpoint URL prefix')"
					:show-trailing-button="!!state.llm_url_prefix"
					@update:value="onInput"
					@trailing-button-click="state.llm_url_prefix = '' ; onInput()" />
				<NcButton type="tertiary"
					:title="t('integration_ovhai', 'For example: \'abc123-def456\'')">
					<template #icon>
						<HelpCircleIcon />
					</template>
				</NcButton>
			</div>
			<div class="line">
				<NcTextField
					class="input"
					:value.sync="state.llm_extra_params"
					:label="t('integration_ovhai', 'Extra model parameters')"
					:show-trailing-button="!!state.llm_extra_params"
					@update:value="onInput"
					@trailing-button-click="state.llm_extra_params = '' ; onInput()" />
				<NcButton type="tertiary"
					:title="llmExtraParamHint">
					<template #icon>
						<HelpCircleIcon />
					</template>
				</NcButton>
			</div>
		</div>
	</div>
</template>

<script>
import OpenInNewIcon from 'vue-material-design-icons/OpenInNew.vue'
import HelpCircleIcon from 'vue-material-design-icons/HelpCircle.vue'

import OvhIcon from './icons/OvhIcon.vue'

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { delay } from '../utils.js'
import { showSuccess, showError } from '@nextcloud/dialogs'

export default {
	name: 'AdminSettings',

	components: {
		OvhIcon,
		OpenInNewIcon,
		HelpCircleIcon,
		NcButton,
		NcTextField,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_ovhai', 'admin-config'),
			llmExtraParamHint: t('integration_ovhai', 'Extra parameters are model-specific. For example: {example}', { example: '{"max_tokens":128,"temperature":0.7}' }),
		}
	},

	watch: {
	},

	mounted() {
	},

	methods: {
		onInput() {
			delay(() => {
				this.saveOptions({
					api_key: this.state.api_key,
					llm_model_name: this.state.llm_model_name,
					llm_url_prefix: this.state.llm_url_prefix,
					llm_extra_params: this.state.llm_extra_params,
				})
			}, 2000)()
		},
		saveOptions(values) {
			const req = {
				values,
			}
			const url = generateUrl('/apps/integration_ovhai/admin-config')
			axios.put(url, req)
				.then((response) => {
					showSuccess(t('integration_ovhai', 'OVH admin options saved'))
				})
				.catch((error) => {
					showError(t('integration_ovhai', 'Failed to save OVH admin options'))
					console.error(error)
				})
		},
	},
}
</script>

<style scoped lang="scss">
#ovhai_prefs {
	#ovh-content {
		margin-left: 40px;
	}
	h2,
	.line,
	.settings-hint {
		display: flex;
		align-items: center;
		margin-top: 12px;
		.icon {
			margin-right: 4px;
		}
	}

	h2 .icon {
		margin-right: 8px;
	}

	h3 {
		font-weight: bold;
		margin-top: 24px;
	}

	.line {
		gap: 8px;
		> .input {
			width: 500px;
		}
	}
}
</style>
