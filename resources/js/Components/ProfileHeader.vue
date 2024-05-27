<template>

    <div class="grid grid-flow-col gap-4">
      <div class="overflow-hidden rounded-lg bg-white shadow col-span-6">
        <div class="bg-white p-6">
          <div class="sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex sm:space-x-5">
              <div class="flex-shrink-0">
                <img class="mx-auto h-20 w-20 rounded-full" :src="user.imageUrl" alt=""/>
              </div>
              <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                <p class="text-xl font-bold text-gray-900 sm:text-2xl">{{ name }}</p>
                <p class="text-sm font-medium text-gray-600">{{user.email}} | {{user.phone}}</p>
                <p class="text-sm font-medium text-gray-600">{{ $t('profile.register_from')+ user.created_at }}</p>
              </div>
            </div>
            <div class="mt-5 grid gap-2 sm:mt-0">
              <Link :href="route('profile.edit')"
                 class="flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                 v-text="$t('profile.settings')"/>
              <Link :href="route('logout')"
                 class="flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                 v-text="$t('profile.logout')"/>

            </div>
          </div>
        </div>
      </div>
      <div class="flex col-span-2 bg-[#E3F1E6]  rounded-2xl">
        <div class="w-1/3 p-6">
          <Icon icon="defaultPoint" class="w-16 h-16"/>
        </div>
        <div class="p-6">
          <h2 class="text-primary-500 text-2xl font-bold">{{contributions_count}}</h2>
          <p class="text-primary-500" v-text="$t('profile.contributions')"></p>
        </div>
      </div>
    </div>


</template>
<script setup>
import {usePage, Link} from "@inertiajs/vue3";
import Icon from "@/Components/Icon.vue";

const user = usePage().props.user;
const name = user.firstname + ' ' + user.lastname;

const props = defineProps({
 contributions_count:{
     type:Number,
     required:true
 }
});
</script>
