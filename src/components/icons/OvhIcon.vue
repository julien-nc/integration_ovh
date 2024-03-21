<template>
	<span :aria-hidden="!title"
		:aria-label="title"
		class="material-design-icon ovh-icon"
		role="img"
		v-bind="$attrs"
		@click="$emit('click', $event)">
		<svg
			:fill="fillColor"
			:width="size"
			:height="size"
			enable-background="new 0 0 150 150"
			version="1.1"
			viewBox="0 0 150 150"
			xml:space="preserve"
			xmlns="http://www.w3.org/2000/svg">
			<g transform="matrix(1.25 0 0 -1.25 -143.83 252.41)">
				<g transform="translate(223.16 170.91)">
					<path d="m0 0-11.769-20.452h-12.361l-14.588-25.314h12.382l-9.365-16.261h30.413c7.774 9.502 12.443 21.645 12.443 34.879 0 9.874-2.609 19.132-7.155 27.148" />
				</g>
				<g transform="translate(162.92 108.88)">
					<path d="m0 0h0.03l-0.015 0.027 37.79 65.633h-31.969l-21.795-37.878-19.727 34.276c-4.557-8.022-7.173-17.292-7.173-27.179 0-13.235 4.668-25.377 12.443-34.879z" />
				</g>
			</g>
		</svg>
	</span>
</template>

<script>
export default {
	name: 'OvhIcon',
	props: {
		title: {
			type: String,
			default: '',
		},
		fillColor: {
			type: String,
			default: 'currentColor',
		},
		size: {
			type: Number,
			default: 24,
		},
	},
}
</script>
