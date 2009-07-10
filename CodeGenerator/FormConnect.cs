using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace GURUCORE.GForm.CodeGenerator
{
    public partial class FormConnect : Form
    {
        public FormConnect()
        {
            InitializeComponent();
        }

        protected SchemaReader m_oSchemaReader;
        public SchemaReader SchemaReader
        {
            get
            {
                return m_oSchemaReader;
            }
        }
    }
}
