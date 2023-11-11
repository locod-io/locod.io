<!--
/*
* This file is part of the Lodoc.io software.
*
* (c) Koen Caerels
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
-->

<template>
  <div v-if="editor" id="editorToolbar1"
       class="flex gap-2 mt-2 bg-white dark:bg-gray-900 px-1 py-1 dark:bg-gray-800 border-b-[1px] border-gray-300 dark:border-gray-600">

    <div @click="editor.chain().focus().toggleBold().run()"
         class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('bold') }">
      <font-awesome-icon icon="fa-solid fa-bold"/>
    </div>
    <div @click="editor.chain().focus().toggleItalic().run()"
         class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2.5 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('italic') }">
      <font-awesome-icon icon="fa-solid fa-italic"/>
    </div>
    <div @click="editor.chain().focus().toggleStrike().run()"
         class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('strike') }">
      <font-awesome-icon icon="fa-solid fa-strikethrough"/>
    </div>
    <div @click="editor.chain().focus().setParagraph().run()"
         class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('paragraph') }">
      <font-awesome-icon icon="fa-solid fa-paragraph"/>
    </div>
    <div @click="editor.chain().focus().toggleHighlight().run()"
         class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('highlight') }">
      <font-awesome-icon icon="fa-solid fa-highlighter"/>
    </div>
    <div @click="editor.chain().focus().toggleBulletList().run()"
         class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('bulletList') }">
      <font-awesome-icon icon="fa-solid fa-list-ul"/>
    </div>
    <div @click="editor.chain().focus().toggleOrderedList().run()"
         class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
         :class="{ 'editor-is-active': editor.isActive('orderedList') }">
      <font-awesome-icon icon="fa-solid fa-list-ol"/>
    </div>

    <button @click="editor.chain().focus().toggleCode().run()"
            class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('code') }">
      <font-awesome-icon icon="fa-solid fa-code"/>
    </button>
    <button @click="editor.chain().focus().toggleCodeBlock().run()"
            class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('codeBlock') }">
      <font-awesome-icon icon="fa-solid fa-terminal"/>
    </button>
    <button @click="editor.chain().focus().toggleBlockquote().run()"
            class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
            :class="{ 'editor-is-active': editor.isActive('blockquote') }">
      <font-awesome-icon icon="fa-solid fa-quote-right"/>
    </button>

    <div @click="editor.chain().focus().unsetAllMarks().run()"
         class="ml-6 bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer">
      <font-awesome-icon icon="fa-solid fa-text-slash"/>
    </div>
    <div @click="editor.chain().focus().clearNodes().run()"
         class="bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer">
      <font-awesome-icon icon="fa-solid fa-rectangle-xmark"/>
    </div>
  </div>

  <div v-if="editor"
       id="editorToolbar2"
       class="gap-2 flex bg-white dark:bg-gray-900 px-1 pt-0.5 pb-1 dark:bg-gray-800 border-b-[1px] border-gray-300 dark:border-gray-600">

    <div v-for="i in 6">
      <button @click="editor.chain().focus().toggleHeading({ level: i }).run()"
              class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer text-xs py-0.5"
              :class="{ 'editor-is-active': editor.isActive('heading', { level: i }) }">
        H{{i}}
      </button>
    </div>

    <div class="mt-0.5 ml-4">
      <button @click="setLink"
              class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
              :class="{ 'editor-is-active': editor.isActive('link') }">
        <font-awesome-icon :icon="['fas', 'link']" />
      </button>
    </div>
    <div class="mt-0.5">
      <button @click="editor.chain().focus().unsetLink().run()"
              class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
              :disabled="!editor.isActive('link')">
        <font-awesome-icon :icon="['fas', 'link-slash']" />
      </button>
    </div>

    <div class="mt-0.5 ml-4">
      <button @click="editor.chain().focus().setTextAlign('left').run()"
              class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
              :class="{ 'editor-is-active': editor.isActive({ textAlign: 'left' }) }">
        <font-awesome-icon :icon="['fas', 'align-left']" />
      </button>
    </div>
    <div class="mt-0.5">
      <button @click="editor.chain().focus().setTextAlign('center').run()"
              class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
              :class="{ 'editor-is-active': editor.isActive({ textAlign: 'center' }) }">
        <font-awesome-icon :icon="['fas', 'align-center']" />
      </button>
    </div>
    <div class="mt-0.5">
      <button @click="editor.chain().focus().setTextAlign('right').run()"
              class=" bg-gray-300 dark:bg-gray-900 rounded-full px-2 cursor-pointer"
              :class="{ 'editor-is-active': editor.isActive({ textAlign: 'right' }) }">
        <font-awesome-icon :icon="['fas', 'align-right']" />
      </button>
    </div>
  </div>


  <div v-if="editor" id="editorToolbarTable"
       class="gap-4 flex bg-white dark:bg-gray-900 px-1 dark:bg-gray-800 border-b-[1px] border-gray-300 dark:border-gray-600 text-xl">

    <button @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()">
      <i class="icons8-insert-table"></i>
    </button>
    <!-- columns -->
    <button @click="editor.chain().focus().addColumnBefore().run()">
      <i class="icons8-insert-column-left"></i>
    </button>
    <button @click="editor.chain().focus().addColumnAfter().run()">
      <i class="icons8-insert-column-right"></i>
    </button>
    <button @click="editor.chain().focus().deleteColumn().run()">
      <i class="icons8-delete-column"></i>
    </button>
    <button @click="editor.chain().focus().addRowBefore().run()">
      <i class="icons8-insert-row-above"></i>
    </button>
    <button @click="editor.chain().focus().addRowAfter().run()">
      <i class="icons8-insert-row"></i>
    </button>
    <button @click="editor.chain().focus().deleteRow().run()">
      <i class="icons8-delete-row"></i>
    </button>
    <button @click="editor.chain().focus().mergeCells().run()">
      <i class="icons8-merge-cells"></i>
    </button>
    <button @click="editor.chain().focus().splitCell().run()">
      <i class="icons8-split-cells"></i>
    </button>
    <button @click="editor.chain().focus().toggleHeaderColumn().run()">
      <i class="icons8-select-column"></i>
    </button>
    <button @click="editor.chain().focus().toggleHeaderRow().run()">
      <i class="icons8-select-row"></i>
    </button>
    <button @click="editor.chain().focus().toggleHeaderCell().run()">
      <i class="icons8-select-cell"></i>
    </button>
    <button @click="editor.chain().focus().fixTables().run()">
      <i class="icons8-cells"></i>
    </button>
    <button @click="editor.chain().focus().deleteTable().run()">
      <i class="icons8-delete-table"></i>
    </button>
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
import Table from '@tiptap/extension-table'
import TableCell from '@tiptap/extension-table-cell'
import TableHeader from '@tiptap/extension-table-header'
import TableRow from '@tiptap/extension-table-row'
import HorizontalRule from '@tiptap/extension-horizontal-rule'
import {Editor, EditorContent} from '@tiptap/vue-3'
import Heading from "@tiptap/extension-heading";
import HardBreak from "@tiptap/extension-hard-break";
import Link from '@tiptap/extension-link'

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
  methods: {
    setLink() {
      const previousUrl = this.editor.getAttributes('link').href
      const url = window.prompt('URL', previousUrl)
      // cancelled
      if (url === null) {
        return
      }
      // empty
      if (url === '') {
        this.editor
            .chain()
            .focus()
            .extendMarkRange('link')
            .unsetLink()
            .run()
        return
      }
      // update link
      this.editor
          .chain()
          .focus()
          .extendMarkRange('link')
          .setLink({ href: url , target: '_blank'})
          .run()
    },
  },
  mounted() {
    this.editor = new Editor({
      extensions: [
        StarterKit,
        Highlight,
        Typography,
        TextAlign.configure({
          alignments: ['left', 'right','center'],types: ['heading', 'paragraph'],
        }),
        Table.configure({
          resizable: true,
        }),
        TableRow,
        TableHeader,
        TableCell,
        HorizontalRule,
        Heading.configure({
          levels: [1, 2, 3, 4, 5, 6],
        }),
        HardBreak,
        Link.configure({
          openOnClick: false,
        }),
      ],
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