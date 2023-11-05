<template>
	<div class="absolute bg-white border border-black z-50 top-5 left-10 p-6 w-96">
		<div class="flex justify-between items-center mb-3.5">
			<h2 class="font-bold text-2xl">{{point.type.display_name}} </h2>
			<desktop-filter-close-icon
				v-on:click="close()"
				class="cursor-pointer"
			></desktop-filter-close-icon>
		</div>

		<p class="text-xs mb-3.5">
			<span v-if="null !== point.service">
				{{point.service.display_name}}
			</span>
			<span v-if="null !== getField('managed_by', 'value')">
				- {{getField('managed_by', 'value')}}
			</span>
		</p>

		<div class="flex items-center justify-between mb-6">
			<span class="inline-flex items-center rounded-full bg-[#656565] px-4 py-1 text-xs font-medium text-white">Validat</span>
			<button type="button" class="text-xs underline" v-on:click="reportProblem();">{{CONSTANTS.LABELS.POINT_DETAILS.REPORT_PROBLEM_LABEL}}</button>
		</div>

		<h3 class="text-sm mb-2 flex items-center gap-x-2 font-semibold">
			<point-details-location-icon></point-details-location-icon>
			{{CONSTANTS.LABELS.POINT_DETAILS.ADDRESS_LABEL}}
		</h3>
		<div class="flex justify-between items-center mb-6">
			<p class="text-xs">{{getField('address', 'value')}}</p>
			<a v-on:click="goToMap()" class="cursor-pointer">
				<point-details-address-icon></point-details-address-icon>
			</a>

		</div>
		<template v-if="Object.keys(dailySchedule).length > 0">
			<h3 class="text-sm mb-2 flex items-center gap-x-2 font-semibold">
				<point-details-schedule-icon></point-details-schedule-icon>
				{{CONSTANTS.LABELS.POINT_DETAILS.SCHEDULE_LABEL}}
			</h3>
			<div class="flex justify-between items-center mb-6 grid grid-cols-1 space-y-2">
				<p
					class="text-xs"
					v-for="(hours, day) in dailySchedule"
				>
					{{ showDailySchedule(day, hours) }}
				</p>

			</div>
		</template>
		<template v-if="Object.keys(point.materials).length > 0">
			<h3 class="text-sm mb-2 flex items-center gap-x-2 font-semibold">
				<point-details-materials-icon></point-details-materials-icon>
				{{CONSTANTS.LABELS.POINT_DETAILS.MATERIALS_LABEL}}
			</h3>
			<div class="mb-6">
				<div class="">
					<div class="flex items-center bg-neutral-100 text-black text-xs h-12 pe-4 border">
					<span class="flex items-center justify-center w-11">
						icon
					</span>
						<span class="flex ps-3">Hartie & carton</span>
						<button type="button" class="ml-auto">
							<!-- Expand icon, show/hide based on section open state. -->
							<svg class="" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M14.7071 12.7071C14.3166 13.0976 13.6834 13.0976 13.2929 12.7071L10 9.41421L6.70711 12.7071C6.31658 13.0976 5.68342 13.0976 5.29289 12.7071C4.90237 12.3166 4.90237 11.6834 5.29289 11.2929L9.29289 7.29289C9.68342 6.90237 10.3166 6.90237 10.7071 7.29289L14.7071 11.2929C15.0976 11.6834 15.0976 12.3166 14.7071 12.7071Z" fill="#6B7280"/>
							</svg>

							<!-- Collapse icon, show/hide based on section open state. -->
							<svg class="hidden" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 7.29289C5.68342 6.90237 6.31658 6.90237 6.7071 7.29289L9.99999 10.5858L13.2929 7.29289C13.6834 6.90237 14.3166 6.90237 14.7071 7.29289C15.0976 7.68342 15.0976 8.31658 14.7071 8.70711L10.7071 12.7071C10.3166 13.0976 9.68341 13.0976 9.29289 12.7071L5.29289 8.70711C4.90237 8.31658 4.90237 7.68342 5.29289 7.29289Z" fill="#6B7280"/>
							</svg>
						</button>
					</div>

					<div class="flex items-center bg-white text-xs h-8 pe-4 border">
                            <span class="flex items-center justify-center w-11">

                            </span>
						<span class="flex ps-3 underline">Hartie</span>
					</div>
					<div class="flex items-center bg-white text-xs h-8 pe-4 border">
                            <span class="flex items-center justify-center w-11">

                            </span>
						<span class="flex ps-3 underline">Carton</span>
					</div>
				</div>

				<div class="">
					<div class="flex items-center bg-neutral-100 text-black text-xs h-12 pe-4 border">
                            <span class="flex items-center justify-center w-11">
                                icon
                            </span>
						<span class="flex ps-3">Plastic & PET</span>
						<button type="button" class="ml-auto">
							<!-- Expand icon, show/hide based on section open state. -->
							<svg class="" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M14.7071 12.7071C14.3166 13.0976 13.6834 13.0976 13.2929 12.7071L10 9.41421L6.70711 12.7071C6.31658 13.0976 5.68342 13.0976 5.29289 12.7071C4.90237 12.3166 4.90237 11.6834 5.29289 11.2929L9.29289 7.29289C9.68342 6.90237 10.3166 6.90237 10.7071 7.29289L14.7071 11.2929C15.0976 11.6834 15.0976 12.3166 14.7071 12.7071Z" fill="#6B7280"/>
							</svg>

							<!-- Collapse icon, show/hide based on section open state. -->
							<svg class="hidden" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 7.29289C5.68342 6.90237 6.31658 6.90237 6.7071 7.29289L9.99999 10.5858L13.2929 7.29289C13.6834 6.90237 14.3166 6.90237 14.7071 7.29289C15.0976 7.68342 15.0976 8.31658 14.7071 8.70711L10.7071 12.7071C10.3166 13.0976 9.68341 13.0976 9.29289 12.7071L5.29289 8.70711C4.90237 8.31658 4.90237 7.68342 5.29289 7.29289Z" fill="#6B7280"/>
							</svg>
						</button>
					</div>
				</div>

				<div class="">
					<div class="flex items-center bg-neutral-100 text-black text-xs h-12 pe-4 border">
                            <span class="flex items-center justify-center w-11">
                                icon
                            </span>
						<span class="flex ps-3">Metal & aluminiu</span>
						<button type="button" class="ml-auto">
							<!-- Expand icon, show/hide based on section open state. -->
							<svg class="" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M14.7071 12.7071C14.3166 13.0976 13.6834 13.0976 13.2929 12.7071L10 9.41421L6.70711 12.7071C6.31658 13.0976 5.68342 13.0976 5.29289 12.7071C4.90237 12.3166 4.90237 11.6834 5.29289 11.2929L9.29289 7.29289C9.68342 6.90237 10.3166 6.90237 10.7071 7.29289L14.7071 11.2929C15.0976 11.6834 15.0976 12.3166 14.7071 12.7071Z" fill="#6B7280"/>
							</svg>

							<!-- Collapse icon, show/hide based on section open state. -->
							<svg class="hidden" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 7.29289C5.68342 6.90237 6.31658 6.90237 6.7071 7.29289L9.99999 10.5858L13.2929 7.29289C13.6834 6.90237 14.3166 6.90237 14.7071 7.29289C15.0976 7.68342 15.0976 8.31658 14.7071 8.70711L10.7071 12.7071C10.3166 13.0976 9.68341 13.0976 9.29289 12.7071L5.29289 8.70711C4.90237 8.31658 4.90237 7.68342 5.29289 7.29289Z" fill="#6B7280"/>
							</svg>
						</button>
					</div>
				</div>
			</div>
		</template>

		<template v-if="getField('notes', 'value') != ''">
			<h3 class="text-sm mb-2 flex items-center gap-x-2 font-semibold">
				<point-details-notes-icon></point-details-notes-icon>
				{{CONSTANTS.LABELS.POINT_DETAILS.NOTES_LABEL}}
			</h3>
			<p class="text-xs mb-1.5">
				{{getField('notes', 'value')}}
			</p>
		</template>
		<div class="flex flex-row gap-x-4 mb-6">
			<span
				class="text-[#757575] text-xs flex gap-x-2 items-center"
				v-if="getField('offers_transport', 'value') !== null"
			>
				<point-details-transport-icon></point-details-transport-icon>
				{{CONSTANTS.LABELS.POINT_DETAILS.TRANSPORT_LABEL}}
			</span>
			<span
				class="text-[#757575] text-xs flex gap-x-2 items-center"
				v-if="getField('offers_money', 'value') !== null"
			>
				<point-details-money-icon></point-details-money-icon>
				{{CONSTANTS.LABELS.POINT_DETAILS.MONEY_LABEL}}
			</span>
		</div>

		<template v-if="getField('website', 'value') != ''">
			<h3 class="text-sm mb-2 flex items-center gap-x-2 font-semibold">
				<point-details-website-icon></point-details-website-icon>
				{{CONSTANTS.LABELS.POINT_DETAILS.WEBSITE_LABEL}}
			</h3>
			<a :href="getField('website', 'value')" target="_blank" class="text-xs flex mb-6">{{getField('website', 'value')}}</a>
		</template>

		<template v-if="getField('phone_no', 'value') != ''">
			<h3 class="text-sm mb-2 flex items-center gap-x-2 font-semibold">
				<point-details-phone-icon></point-details-phone-icon>
				{{CONSTANTS.LABELS.POINT_DETAILS.PHONE_LABEL}}
			</h3>
			<a :href="`tel:${getField('phone_no', 'value')}`" class="text-xs flex mb-6 underline">{{getField('phone_no', 'value')}}</a>
		</template>

		<h3 class="text-sm mb-2 flex items-center gap-x-2 font-semibold">
			<point-details-share-icon></point-details-share-icon>
			{{CONSTANTS.LABELS.POINT_DETAILS.SHARE_LABEL}}
		</h3>
		<div class="mt-2 flex rounded-md shadow-sm">
			<div class="relative flex flex-grow items-stretch focus-within:z-10">
				<input
					type="text"
					name="email"
					id="link"
					class="block w-full rounded-l-md border-0 py-1.5 text-black ring-1 ring-inset ring-gray-300 placeholder:text-gray-400"
					placeholder="link"
					:value="generatePointLink()"
				>
			</div>
			<button
				type="button"
				class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-black bg-neutral-100 ring-1 ring-inset ring-gray-300"
				v-on:click="copyPointLink()"
			>
				{{CONSTANTS.LABELS.POINT_DETAILS.COPY_LABEL}}
			</button>
		</div>

	</div> <!-- -->
</template>
<script>
import {CONSTANTS} from "../constants.js";
import PointDetailsLocationIcon from "./svg-icons/pointDetailsLocationIcon.vue";
import PointDetailsAddressIcon from "./svg-icons/pointDetailsAddressIcon.vue";
import PointDetailsScheduleIcon from "./svg-icons/pointDetailsScheduleIcon.vue";
import DesktopFilterCloseIcon from "./svg-icons/desktopFilterCloseIcon.vue";
import _ from 'lodash';
import PointDetailsMaterialsIcon from "./svg-icons/pointDetailsMaterialsIcon.vue";
import PointDetailsNotesIcon from "./svg-icons/pointDetailsNotesIcon.vue";
import PointDetailsMoneyIcon from "./svg-icons/pointDetailsMoneyIcon.vue";
import PointDetailsTransportIcon from "./svg-icons/pointDetailsTransportIcon.vue";
import PointDetailsWebsiteIcon from "./svg-icons/pointDetailsWebsiteIcon.vue";
import PointDetailsPhoneIcon from "./svg-icons/pointDetailsPhoneIcon.vue";
import PointDetailsShareIcon from "./svg-icons/pointDetailsShareIcon.vue";

export default
{
	props:
	{
		point:
		{
			type: Object,
			required: true
		}
	},
	components:
	{
		PointDetailsShareIcon,
		PointDetailsPhoneIcon,
		PointDetailsWebsiteIcon,
		PointDetailsTransportIcon,
		PointDetailsMoneyIcon,
		PointDetailsNotesIcon,
		PointDetailsMaterialsIcon,
		DesktopFilterCloseIcon,
		PointDetailsScheduleIcon,
		PointDetailsAddressIcon,
		PointDetailsLocationIcon
	},
	data ()
	{
		return {
			dailySchedule: {}
		};
	},
	methods:
		{
			getField(element, key)
			{
				for (let field of this.point.fields)
				{
					if ('field' in field && field.field.field_name == element)
					{
						if (key in field)
						{
							return field[key];
						}
					}
				}

				return null;
			},
			goToMap()
			{
				window.location = 'https://www.google.com/maps?daddr='+this.point.lat+','+this.point.lon+'&amp;ll&zoom=18';
			},
			close()
			{
				this.$emit('closePointDetails');
			},
			showDailySchedule(day, hours)
			{
				let currentDay = _.get(CONSTANTS, ['LABELS', 'DAYS_OF_WEEK', day], '');
				let startHour = _.get(hours, 'startHour', '');
				let endHour = _.get(hours, 'endHour', '');
				let unknown = _.get(hours, 'unknown', false);

				if (unknown)
				{
					return `${currentDay}: `+CONSTANTS.LABELS.POINT_DETAILS.CLOSED;
				}
				else
				{
					return `${currentDay}: ${startHour} - ${endHour}`;
				}
			},
			generatePointLink()
			{
				return CONSTANTS.APP_DOMAIN + '/point/' + this.point.id;
			},
			copyPointLink()
			{
				let link = document.getElementById('link');
				link.select();
				link.setSelectionRange(0, 99999);
				document.execCommand("copy");
				alert(CONSTANTS.LABELS.POINT_DETAILS.LINK_COPIED);
			},
		},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS
		}
	},
	mounted()
	{
		if (this.getField('opening_hours', 'value'))
		{
			this.dailySchedule = JSON.parse(this.getField('opening_hours', 'value'));
		}
	},
};
</script>
