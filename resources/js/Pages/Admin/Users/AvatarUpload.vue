<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import vueFilePond, { setOptions } from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';
import BaseButton from "@/Components/Admin/BaseButton.vue";

const modalTitle = "Change Avatar";
const props = defineProps(['user']);
const emit = defineEmits(['uploadSuccess']);

const configureFilePond = () => {
  const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImageExifOrientation,
    FilePondPluginImagePreview,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
    FilePondPluginImageTransform,
  );

  setOptions({
    instantUpload: false,
    allowReplace: true,
    allowRevert: false,
  });

  return FilePond;
};

const form = useForm({
  avatar: null,
});

const handleAvatarUpload = (fieldName, file, metadata, load, error, progress, abort) => {
  form.avatar = file;

  form.post(route('admin.users.upload-avatar', props.user), {
    onProgress: (e) => progress(e.percentage, e.loaded, e.total),
    onSuccess: (page) => {
      load(page.props.avatar_url);
      emit('uploadSuccess', page.props.avatar_url);
    },
    onError: () => {
      error('Failed to upload avatar');
    },
    onCancelToken: (cancel) => {
      return {
        abort: () => {
          cancel();
          abort();
        },
      };
    },
  });

  return {
    abort: () => {
      form.cancel();
    },
  };
};

const filePondOptions = computed(() => ({
  name: 'avatar',
  acceptedFileTypes: ['image/jpeg', 'image/png'],
  labelIdle: "Drag & Drop your avatar, or <span class='filepond--label-action'>Browse</span>",
  imagePreviewHeight: 170,
  imageCropAspectRatio: '1:1',
  imageResizeTargetWidth: 200,
  imageResizeTargetHeight: 200,
  stylePanelLayout: 'compact circle',
  styleLoadIndicatorPosition: 'center bottom',
  styleProgressIndicatorPosition: 'center bottom',
  styleButtonRemoveItemPosition: 'left bottom',
  styleButtonProcessItemPosition: 'center bottom',
  server: { process: handleAvatarUpload },
}));

const FilePond = configureFilePond();
const pondRef = ref(null);

const startUpload = () => {
  if (pondRef.value) {
    pondRef.value.processFiles();
  }
};
</script>

<template>
    <Modal
        ref="userModalRef"
        #default="{ close }"
        maxWidth="sm"
    >
        <div class="flex flex-col justify-center">
            <FilePond
                class="w-40 h-40 bg-transparent mx-auto mb-10"
                ref="pondRef"
                v-bind="filePondOptions"
                :allowReplace="true"
            />
            <BaseButton label="Upload" color="info" :processing="false" @click="startUpload" />
        </div>

    </Modal>
</template>

<style>
.filepond--panel-root {
  @apply bg-[url(props('user').avatar_url)];
}
</style>
