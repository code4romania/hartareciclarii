<template>
	<div class="min-h-full w-full">
		<Disclosure as="nav" class="bg-black" v-slot="{ open }">
		<div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
			<div class="flex h-16 items-center justify-between">
				<div class="flex items-center">
					<div class="flex-shrink-0 cursor-pointer" v-on:click="$router.push('/');">
						<img class="h-8 w-auto" src="/assets/images/logo_white.png" alt="Harta Reciclarii" />
					</div>
				</div>
				<div class="hidden sm:ml-6 sm:block">
					<div class="flex items-center">

						<!-- Profile dropdown -->
						<Menu as="div" class="relative ml-3">
							<div>
								<MenuButton class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
									<span class="absolute -inset-1.5" />
									<span class="sr-only">Open user menu</span>
									<img class="h-8 w-8 rounded-full" v-if="this.userInfo.image != ''" :src="this.userInfo.image" alt="" />
									<span class="h-8 w-8 rounded-full bg-neutral-100 pt-1.5 text-black" v-else>{{ this.userInfo.firstname.substr(0,1) }} {{ this.userInfo.lastname.substr(0,1) }}</span>
								</MenuButton>
							</div>
							<transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
								<MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
									<MenuItem v-slot="{ active }">
										<a href="#" :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
											{{CONSTANTS.LABELS.TOP_MENU.LOGOUT}}
										</a>
									</MenuItem>
								</MenuItems>
							</transition>
						</Menu>
					</div>
				</div>
				<div class="-mr-2 flex sm:hidden">
					<!-- Mobile menu button -->
					<DisclosureButton class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
						<span class="absolute -inset-0.5" />
						<span class="sr-only">Open main menu</span>
						<Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
						<XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
					</DisclosureButton>
				</div>
			</div>
		</div>

		<DisclosurePanel class="sm:hidden">
			<div class="border-t border-gray-700 pb-3 pt-4">
				<div class="flex items-center px-5">
					<div class="flex-shrink-0">
						<img class="h-10 w-10 rounded-full" v-if="this.userInfo.image != ''" :src="this.userInfo.image" alt="" />
						<span class="h-10 w-10 rounded-full bg-neutral-100 pt-2 text-black" v-else>{{ this.userInfo.firstname.substr(0,1) }} {{ this.userInfo.lastname.substr(0,1) }}</span>
					</div>
					<div class="ml-3">
						<div class="text-base font-medium text-white">{{this.userInfo.firstname}} {{this.userInfo.lastname}}</div>
						<div class="text-sm font-medium text-gray-400">{{this.userInfo.email}}</div>
					</div>
				</div>
				<div class="mt-3 space-y-1 px-2">
					<DisclosureButton
						as="a"
						class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white cursor-pointer"
					>
						{{CONSTANTS.LABELS.TOP_MENU.LOGOUT}}
					</DisclosureButton>
				</div>
			</div>
		</DisclosurePanel>
	</Disclosure>
		<div class="pt-5">
			<header>
				<div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
					<a class="text-sm font-medium text-black leading-none cursor-pointer" v-on:click="$router.push('/');">
						<arrow-left-icon class="h-5 w-5 text-black inline"></arrow-left-icon>
						<span class="pt-2 ml-1 inline">{{CONSTANTS.LABELS.TOP_MENU.BACK}}</span>
					</a>
				</div>
			</header>
			<main>
				<div class="mx-auto max-w-full sm:px-6 lg:px-8 py-10">
					<h1 class="text-black text-2xl font-bold leading-none mb-10">
						{{CONSTANTS.LABELS.PROFILE.HEADING}}
					</h1>
					<div class="grid grid-cols-5 gap-4">
						<div class="lg:w-44 w-full">
							<div class="relative bg-gray-300 rounded-lg">
								<div class="w-28 h-28"></div>
								<div class="w-7 h-7"></div>
							</div>
						</div>
						<div>
							<div class="text-black text-2xl font-normal leading-none">{{this.userInfo.firstname}} {{this.userInfo.lastname}}</div>
							<div class="text-black text-sm font-normal  leading-none">{{CONSTANTS.LABELS.PROFILE.MEMBER_FROM}} {{moment(this.userInfo.created_at).format('DD MMMM YYYY')}}</div>
							<div class="py-5"></div>
							<div class="text-gray-700 text-sm font-medium leading-tight">{{CONSTANTS.LABELS.PROFILE.EMAIL}}</div>
							<div class="text-black text-sm font-normal leading-none">{{this.userInfo.email}}</div>
						</div>
						<div>
							<a v-on:click="editUser()" class="cursor-pointer">
								<pencil-square-icon class="h-5 w-5 text-black inline"></pencil-square-icon>
								{{CONSTANTS.LABELS.PROFILE.EDIT}}
							</a>
						</div>
						<div class="col-span-2 self-center content-end items-end">
							<div class="flex self-center content-center justify-end">
								<div class=" relative bg-neutral-100 rounded-lg py-6 pl-6 pr-16">
									<span class="text-stone-500 text-8xl font-bold leading-none">
										<span v-if="Object.keys(userInfo).length > 0 && 'contributions' in userInfo">
											{{userInfo.contributions.length}}
										</span>
										<span v-else>0</span>
									</span><br />
									<span class="text-stone-500 text-3xl font-normal leading-none">{{CONSTANTS.LABELS.PROFILE.CONTRIBUTIONS}}</span>
								</div>
							</div>
						</div>
					</div>
					<template v-if="Object.keys(userInfo).length > 0 && 'contributions' in userInfo && userInfo.contributions.length > 0">
						<h2 class="text-black text-lg font-semibold leading-none my-10">
							{{CONSTANTS.LABELS.PROFILE.CONTRIBUTIONS_TITLE}}
						</h2>
						<div class="flow-root">
							<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
								<div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
									<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
										<table class="min-w-full divide-y divide-gray-300">
											<thead class="bg-gray-50">
											<tr>
												<th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">{{CONSTANTS.LABELS.PROFILE.TABLE_HEADING.DATE_HOUR}}</th>
												<th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{CONSTANTS.LABELS.PROFILE.TABLE_HEADING.CONTRIBUTION_TYPE}}</th>
												<th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{CONSTANTS.LABELS.PROFILE.TABLE_HEADING.ITEM_TYPE}}</th>
												<th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{CONSTANTS.LABELS.PROFILE.TABLE_HEADING.LOCATION}}</th>
												<th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{CONSTANTS.LABELS.PROFILE.TABLE_HEADING.POINT_ID}}</th>
												<th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{CONSTANTS.LABELS.PROFILE.TABLE_HEADING.ACTIONS}}</th>
											</tr>
											</thead>
											<tbody class="divide-y divide-gray-200 bg-white">
											<tr v-for="contribution in userInfo.contributions">
												<td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ moment(contribution.date).format('DD MMMM YYYY / hh:mm') }}</td>
												<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ contribution.type }}</td>
												<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ contribution.item_type }}</td>
												<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ contribution.location }}</td>
												<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ contribution.point_id }}</td>
												<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
													<a
														v-if="contribution.status == 1"
														v-on:click="viewOnMap(contribution.point_id)"
														class="cursor-pointer text-black text-xs font-normal underline leading-none"
													>
														{{CONSTANTS.LABELS.PROFILE.TABLE_CONTENT.VIEW_ON_MAP}}
													</a>
													<span v-else>
														{{CONSTANTS.LABELS.PROFILE.TABLE_CONTENT.PENDING}}
													</span>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</template>
				</div>
			</main>
		</div>
	</div>
</template>

<script>
import {CONSTANTS} from "../constants.js";
import {getUserProfile} from "../general.js";
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ArrowLeftIcon, PencilSquareIcon } from '@heroicons/vue/20/solid'
import moment from 'moment';

export default
{
	components:
	{
		MenuItem,
		MenuItems,
		Disclosure,
		MenuButton,
		DisclosurePanel,
		DisclosureButton,
		Bars3Icon,
		BellIcon,
		XMarkIcon,
		Menu,
		ArrowLeftIcon,
		PencilSquareIcon
	},
	data ()
	{
		return {
			userInfo: {},
			moment,
			isAuthenticated: false,
		};
	},
	methods:
	{
		async getUserInfo ()
		{
			this.userInfo = await getUserProfile();
			console.log(`user`, this.userInfo);

			if (Object.keys(this.userInfo).length > 0)
			{
				this.isAuthenticated = true;
			}
			else
			{
				this.$router.push('/');
			}
		},
		viewOnMap(id)
		{
			this.$router.push('/point/'+id);
		}
	},
	mounted ()
	{
		this.getUserInfo();
	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS
		}
	}
};
</script>

