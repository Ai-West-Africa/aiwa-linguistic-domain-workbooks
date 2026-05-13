#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WORKBOOK_DIR="${ROOT_DIR}/workbooks"
OUTPUT_DIR="${1:-${ROOT_DIR}/release-assets/workbooks}"
REFERENCE_DOC="${2:-}"
CSS_FILE="${ROOT_DIR}/styles/workbook-print.css"

if ! command -v pandoc >/dev/null 2>&1; then
  echo "pandoc is required to export workbook files." >&2
  exit 1
fi

if ! command -v weasyprint >/dev/null 2>&1; then
  echo "weasyprint is required to export workbook PDF files." >&2
  exit 1
fi

if [[ ! -d "${WORKBOOK_DIR}" ]]; then
  echo "Workbook directory not found: ${WORKBOOK_DIR}" >&2
  exit 1
fi

mkdir -p "${OUTPUT_DIR}"
shopt -s nullglob
workbook_files=("${WORKBOOK_DIR}"/*.md)

if [[ ${#workbook_files[@]} -eq 0 ]]; then
  echo "No workbook markdown files found in ${WORKBOOK_DIR}" >&2
  exit 1
fi

for workbook_file in "${workbook_files[@]}"; do
  workbook_name="$(basename "${workbook_file}" .md)"
  docx_output="${OUTPUT_DIR}/${workbook_name}.docx"
  pdf_output="${OUTPUT_DIR}/${workbook_name}.pdf"

  docx_args=(
    "${workbook_file}"
    --from=gfm
    --to=docx
    --standalone
    --metadata=title:"${workbook_name}"
    --variable=mainfont:"Noto Sans"
    --output="${docx_output}"
  )

  if [[ -n "${REFERENCE_DOC}" && -f "${REFERENCE_DOC}" ]]; then
    docx_args+=("--reference-doc=${REFERENCE_DOC}")
  fi

  pandoc "${docx_args[@]}"

  pandoc "${workbook_file}" \
    --from=gfm \
    --to=html \
    --standalone \
    --metadata=title:"${workbook_name}" \
    --pdf-engine=weasyprint \
    --css="${CSS_FILE}" \
    --output="${pdf_output}"

done

echo "Workbook exports generated in ${OUTPUT_DIR}"
