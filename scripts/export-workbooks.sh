#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WORKBOOK_DIR="${ROOT_DIR}/workbooks"
REFERENCE_GENERATOR="${ROOT_DIR}/scripts/create-workbook-reference-docx.py"
DEFAULT_REFERENCE_DOC="${ROOT_DIR}/styles/workbook-reference.docx"
if [[ $# -ge 1 && -n "${1}" ]]; then
  OUTPUT_DIR="${1}"
else
  OUTPUT_DIR="$(mktemp -d "${TMPDIR:-/tmp}/workbooks-export.XXXXXX")"
fi
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

if [[ -z "${REFERENCE_DOC}" ]]; then
  REFERENCE_DOC="${DEFAULT_REFERENCE_DOC}"
fi

if [[ ! -f "${REFERENCE_DOC}" ]]; then
  if [[ -f "${REFERENCE_GENERATOR}" ]] && command -v python3 >/dev/null 2>&1; then
    python3 "${REFERENCE_GENERATOR}" "${REFERENCE_DOC}"
  else
    echo "Reference DOCX not found and generator is unavailable: ${REFERENCE_DOC}" >&2
    exit 1
  fi
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

  docx_args+=("--reference-doc=${REFERENCE_DOC}")

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
