#!/usr/bin/env python3
"""Create a Pandoc reference DOCX with Noto Sans as the default font."""

from __future__ import annotations

import sys
from docx import Document
from docx.shared import Pt


def apply_style(document: Document, style_name: str, size: int) -> None:
    style = document.styles[style_name]
    style.font.name = "Noto Sans"
    style.font.size = Pt(size)


def main() -> int:
    output_path = sys.argv[1] if len(sys.argv) > 1 else "styles/workbook-reference.docx"
    document = Document()

    apply_style(document, "Normal", 11)
    apply_style(document, "Heading 1", 20)
    apply_style(document, "Heading 2", 16)
    apply_style(document, "Heading 3", 13)

    document.save(output_path)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
