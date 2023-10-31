<template>
	<div class="hidden absolute top-0 end-0 px-4 py-6 z-50 gap-x-3 lg:flex">
		<button
            type="button"
            class="flex rounded-full items-center gap-x-2 bg-white px-5 py-1 text-black"
            v-on:click="addMapPoint()"
        >
			<menu-add-point-icon></menu-add-point-icon>
			{{CONSTANTS.LABELS.TOP_MENU.ADD_POINT}}
		</button>
		<Menu as="div" class="relative inline-block text-left">
			<div>
				<MenuButton class="flex rounded-full items-center gap-x-2 bg-white px-5 py-1 text-black">
					<menu-dictionary-icon></menu-dictionary-icon>
					{{CONSTANTS.LABELS.TOP_MENU.DICTIONARY}}
				</MenuButton>
			</div>

			<transition
				enter-active-class="transition ease-out duration-100"
				enter-from-class="transform opacity-0 scale-95"
				enter-to-class="transform opacity-100 scale-100"
				leave-active-class="transition ease-in duration-75"
				leave-from-class="transform opacity-100 scale-100"
				leave-to-class="transform opacity-0 scale-95"
			>
				<MenuItems class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
					<div class="py-1">
						<MenuItem v-slot="{ active }">
							<a v-on:click="goToDictionary()" class="group flex text-gray-700 px-3 gap-x-2 py-2 hover:bg-gray-100 cursor-pointer" target="_blank">
								<menu-dictionary-icon></menu-dictionary-icon>
								{{CONSTANTS.LABELS.TOP_MENU.DICTIONARY_RECYCLING}}
							</a>
						</MenuItem>
						<MenuItem v-slot="{ active }">
							<a v-on:click="goToDictionary()" class="group flex text-gray-700 px-3 gap-x-2 py-2 hover:bg-gray-100 cursor-pointer" target="_blank">
								<menu-dictionary-icon></menu-dictionary-icon>
								{{CONSTANTS.LABELS.TOP_MENU.DICTIONARY}}
							</a>
						</MenuItem>
						<MenuItem v-slot="{ active }">
							<a v-on:click="goToDictionary()" class="group flex text-gray-700 px-3 gap-x-2 py-2 hover:bg-gray-100 cursor-pointer" target="_blank">
								<menu-dictionary-icon></menu-dictionary-icon>
								{{CONSTANTS.LABELS.TOP_MENU.GUIDE_A_Z}}
							</a>
						</MenuItem>
					</div>
				</MenuItems>
			</transition>
		</Menu>

		<button class="flex rounded-full items-center gap-x-2 rounded-md bg-white px-5 py-1 text-black" type="button" v-on:click="goToFaq()">
			<menu-faq-icon></menu-faq-icon>
			{{CONSTANTS.LABELS.TOP_MENU.FAQ}}
		</button>

		<template v-if="!isAuthenticated">
			<button class="flex rounded-full items-center gap-x-2 rounded-md bg-white px-5 py-1 text-black" type="button" v-on:click="openLoginModal();">
				<menu-my-account-icon></menu-my-account-icon>
				{{CONSTANTS.LABELS.TOP_MENU.MY_ACCOUNT}}
			</button>
		</template>
		<template v-if="isAuthenticated">
			<Menu as="div" class="relative inline-block text-left">
				<div>
					<MenuButton class="flex rounded-full items-center gap-x-2 bg-white px-5 py-1 text-black">
						<menu-my-account-icon></menu-my-account-icon>
						{{CONSTANTS.LABELS.TOP_MENU.MY_ACCOUNT}}
					</MenuButton>
				</div>

				<transition
					enter-active-class="transition ease-out duration-100"
					enter-from-class="transform opacity-0 scale-95"
					enter-to-class="transform opacity-100 scale-100"
					leave-active-class="transition ease-in duration-75"
					leave-from-class="transform opacity-100 scale-100"
					leave-to-class="transform opacity-0 scale-95"
				>
					<MenuItems class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
						<div class="py-1">
							<MenuItem v-slot="{ active }">
								<a v-on:click="openLoginModal()" class="group flex text-gray-700 px-3 gap-x-2 py-2 hover:bg-gray-100 cursor-pointer" target="_blank">
									<menu-my-account-icon></menu-my-account-icon>
									{{CONSTANTS.LABELS.TOP_MENU.MY_PROFILE}}
								</a>
							</MenuItem>
							<MenuItem v-slot="{ active }">
								<a v-on:click="logout()" class="group flex text-gray-700 px-3 gap-x-2 py-2 hover:bg-gray-100 cursor-pointer" target="_blank">
									<menu-my-account-icon></menu-my-account-icon>
									{{CONSTANTS.LABELS.TOP_MENU.LOGOUT}}
								</a>
							</MenuItem>
						</div>
					</MenuItems>
				</transition>
			</Menu>
		</template>
	</div>
	<login-modal
		:is-open="isLoginModalOpen"
		:user-info="userInfo"
		:is-authenticated="isAuthenticated"
		@close="isLoginModalOpen=false"
	>

	</login-modal>

    <add-point-modal
        v-if="!resetPointModal"
		:is-open="isAddPointModalOpen"
		:user-info="userInfo"
		:is-authenticated="isAuthenticated"
		@close="isAddPointModalOpen=false"
		@reset="resetAddPointModal()"
	>
	</add-point-modal>
	<!--
	<add-map-point-modal>

	</add-map-point-modal>
	-->
</template>

<script>
import {CONSTANTS} from "@/constants.js";
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon} from '@heroicons/vue/20/solid';
import LoginModal from "./modals/loginModal.vue";
import AddPointModal from "./modals/addPoint/generalModal.vue";
import MenuDictionaryIcon from "./svg-icons/menuDictionaryIcon.vue";
import MenuAddPointIcon from "./svg-icons/menuAddPointIcon.vue";
import MenuFaqIcon from "./svg-icons/menuFaqIcon.vue";
import MenuMyAccountIcon from "./svg-icons/menuMyAccountIcon.vue";
import eventBus from "../eventBus.js";
import _ from "lodash";
import axios, {HttpStatusCode} from "axios";
export default
{
	name: "topMenu",
	components:
	{
		MenuMyAccountIcon,
		MenuFaqIcon,
		MenuAddPointIcon,
		MenuDictionaryIcon,
		LoginModal,
        AddPointModal,
		Menu,
		MenuButton,
		MenuItem,
		MenuItems,
		ChevronDownIcon
	},
	props:
	{
		isAuthenticated:
		{
			type: Boolean,
			required: true,
		},
		userInfo:
		{
			type: Object,
			required: true,
		}
	},
	data()
	{
		return {
			isLoginModalOpen: false,
			isAddPointModalOpen: false,
			active: false,
            resetPointModal: false
		};
	},
	computed: {
		CONSTANTS ()
		{
			return CONSTANTS;
		}
	},
	methods:
	{
        resetAddPointModal() {
            this.resetPointModal = true;
            setTimeout(() => {
                this.resetPointModal = false;
            }, 100);
        },
		goToDictionary()
		{
			location.href = 'https://hartareciclarii.ro/ce-si-cum-reciclez/#/category/all';
		},
		goToFaq()
		{
			location.href = 'https://hartareciclarii.ro/despre-proiect/intrebari-frecvente/';
		},
		addMapPoint()
		{
			this.isAddPointModalOpen = true;
		},
		openLoginModal()
		{
			if (this.isAuthenticated && Object.keys(this.userInfo).length > 0)
			{
				this.$router.push({name: 'userProfile'});
				this.isLoginModalOpen = false;
			}
			else
			{
				this.isLoginModalOpen = true;
			}
		},
		logout()
		{
			axios
				.post(CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.AUTH.LOGOUT)
				.then((response) => {
					localStorage.removeItem('userSession');
					localStorage.removeItem('userToken');
					eventBus.$emit('getUser', false);
				}).catch((err) => {

			});
		}
	}
};
</script>
