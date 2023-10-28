<!--
/*
* This file is part of the Locod.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div v-if="editor" id="editorToolbar"
       class="flex bg-white dark:bg-gray-900 p-1 dark:bg-gray-800 border-b-[1px] border-gray-300 dark:border-gray-600 py-3">

    <div @click="editor.chain().focus().toggleBold().run()"
            class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('bold') }">
      <font-awesome-icon icon="fa-solid fa-bold" />
    </div>
    <div @click="editor.chain().focus().toggleItalic().run()"
            class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2.5 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('italic') }">
      <font-awesome-icon icon="fa-solid fa-italic" />
    </div>
    <div @click="editor.chain().focus().toggleStrike().run()"
            class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('strike') }">
      <font-awesome-icon icon="fa-solid fa-strikethrough" />
    </div>
    <div @click="editor.chain().focus().setParagraph().run()"
         class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('paragraph') }">
      <font-awesome-icon icon="fa-solid fa-paragraph" />
    </div>
    <div @click="editor.chain().focus().toggleHighlight().run()"
         class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('highlight') }">
      <font-awesome-icon icon="fa-solid fa-highlighter" />
    </div>
    <div @click="editor.chain().focus().toggleBulletList().run()"
         class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('bulletList') }">
      <font-awesome-icon icon="fa-solid fa-list-ul" />
    </div>
    <div @click="editor.chain().focus().toggleOrderedList().run()"
         class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('orderedList') }">
      <font-awesome-icon icon="fa-solid fa-list-ol" />
    </div>

    <button @click="editor.chain().focus().toggleCode().run()"
            class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('code') }">
      <font-awesome-icon icon="fa-solid fa-code" />
    </button>
    <button @click="editor.chain().focus().toggleCodeBlock().run()"
            class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('codeBlock') }">
      <font-awesome-icon icon="fa-solid fa-terminal" />
    </button>
    <button @click="editor.chain().focus().toggleBlockquote().run()"
            class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('blockquote') }">
      <font-awesome-icon icon="fa-solid fa-quote-right" />
    </button>

    <div @click="editor.chain().focus().unsetAllMarks().run()"
         class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer">
      <font-awesome-icon icon="fa-solid fa-text-slash" />
    </div>
    <div @click="editor.chain().focus().clearNodes().run()"
         class="mr-2 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer">
      <font-awesome-icon icon="fa-solid fa-rectangle-xmark" />
    </div>
  </div>

  <div class="content-wrapper border-b-[1px] border-gray-300 dark:border-gray-600 dark:bg-gray-900 p-1 bg-white">
    <editor-content :editor="editor"/>
  </div>

</template>

<script>
import StarterKit from '@tiptap/starter-kit'
import Highlight from '@tiptap/extension-highlight'
import Typography from '@tiptap/extension-typography'
import TextAlign from '@tiptap/extension-text-align'
import {Editor, EditorContent} from '@tiptap/vue-3'

export default {
  components: {
    EditorContent,
  },
  props: {
    modelValue: {
      type: String,
      default: '',
    },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      editor: null,
    }
  },
  watch: {
    modelValue(value) {
      const isSame = this.editor.getHTML() === value
      if (isSame) {
        return
      }
      this.editor.commands.setContent(value, false)
    },
  },
  mounted() {
    this.editor = new Editor({
      extensions: [StarterKit,Highlight,Typography,TextAlign,],
      content: this.modelValue,
      onUpdate: () => {
        this.$emit('update:modelValue', this.editor.getHTML())
      },
    })
  },
  beforeUnmount() {
    this.editor.destroy()
  },
}
</script>

<style>

</style>