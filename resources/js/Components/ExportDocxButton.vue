<template>
  <div
    class="inline-flex items-center bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 text-sm font-semibold px-3 py-1 rounded-full cursor-pointer transition hover:scale-105"
    @click="exportDocx"
  >
    <span class="mr-1">📄</span>
    <span>Export DOCX</span>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { Document, Packer, Paragraph, TextRun } from 'docx'
import { saveAs } from 'file-saver'

const props = defineProps({
  article: Object
})

const htmlToParagraphs = (html) => {
  const tempDiv = document.createElement('div')
  tempDiv.innerHTML = html
  const paragraphs = []

  tempDiv.childNodes.forEach(node => {
    if (node.nodeType === 1) { // element
      if (node.tagName === 'P') {
        const children = []
        node.childNodes.forEach(c => {
          if (c.nodeType === 3) {
            children.push(new TextRun({ text: c.textContent }))
          } else if (c.tagName === 'STRONG') {
            children.push(new TextRun({ text: c.textContent, bold: true }))
          } else if (c.tagName === 'EM') {
            children.push(new TextRun({ text: c.textContent, italics: true }))
          }
        })
        paragraphs.push(new Paragraph({ children, spacing: { after: 200 } }))
      } else if (node.tagName.startsWith('H')) {
        paragraphs.push(new Paragraph({
          children: [new TextRun({ text: node.textContent, bold: true, size: 32 })],
          spacing: { after: 300 }
        }))
      } else if (node.tagName === 'BR') {
        paragraphs.push(new Paragraph({ children: [new TextRun({ text: '' })] }))
      }
    } else if (node.nodeType === 3 && node.textContent.trim()) {
      paragraphs.push(new Paragraph({ children: [new TextRun(node.textContent)] }))
    }
  })

  return paragraphs
}

const exportDocx = async () => {
  const contentParagraphs = htmlToParagraphs(props.article.content)

  const doc = new Document({
    sections: [
      {
        children: [
          new Paragraph({ children: [new TextRun({ text: props.article.title, bold: true, size: 36 })], spacing: { after: 300 } }),
          new Paragraph({ children: [new TextRun({ text: `Penulis: ${props.article.author?.name || 'Anonim'}`, italics: true })] }),
          new Paragraph({ children: [new TextRun({ text: 'Sumber: Tikeli.com', italics: true })], spacing: { after: 300 } }),
          ...contentParagraphs,
        ],
      },
    ],
  })

  const blob = await Packer.toBlob(doc)
  saveAs(blob, `${props.article.title}.docx`)
}
</script>
