using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;
using System.IO;
using System.Text;

namespace GURUCORE.GForm.CodeGenerator
{	
	public class Code : System.Windows.Forms.Form
	{
        /// <summary>
        /// the underscore  "_"   sign, prefix of each auto-generated DTO, seperate with user-code addition DTO
        /// </summary>
        public const string PARTIAL_FILE_PREFIX = "_";
        /// <summary>
        /// the    ".gen"     sign, surfix of each auto-generated DTO, seperate with user-code addition DTO
        /// </summary>
        public const string PARTIAL_FILE_SURFIX = ".gen";

		private System.Windows.Forms.TextBox txtCode;
		/// <summary>
		/// Required designer variable.
		/// </summary>
		private System.ComponentModel.Container components = null;

		public Code(string p_sFileName)
		{
			//
			// Required for Windows Form Designer support
			//
			InitializeComponent();
			
			this.Text = p_sFileName;
		}

		/// <summary>
		/// Clean up any resources being used.
		/// </summary>
		protected override void Dispose( bool disposing )
		{
			if( disposing )
			{
				if(components != null)
				{
					components.Dispose();
				}
			}
			base.Dispose( disposing );
		}

		#region Windows Form Designer generated code
		/// <summary>
		/// Required method for Designer support - do not modify
		/// the contents of this method with the code editor.
		/// </summary>
		private void InitializeComponent()
		{
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(Code));
            this.txtCode = new System.Windows.Forms.TextBox();
            this.SuspendLayout();
            // 
            // txtCode
            // 
            this.txtCode.Dock = System.Windows.Forms.DockStyle.Fill;
            this.txtCode.Font = new System.Drawing.Font("Courier New", 9F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCode.Location = new System.Drawing.Point(0, 0);
            this.txtCode.Multiline = true;
            this.txtCode.Name = "txtCode";
            this.txtCode.ScrollBars = System.Windows.Forms.ScrollBars.Both;
            this.txtCode.Size = new System.Drawing.Size(378, 232);
            this.txtCode.TabIndex = 0;
            // 
            // Code
            // 
            this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
            this.ClientSize = new System.Drawing.Size(378, 232);
            this.Controls.Add(this.txtCode);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "Code";
            this.WindowState = System.Windows.Forms.FormWindowState.Maximized;
            this.Closed += new System.EventHandler(this.Code_Closed);
            this.Activated += new System.EventHandler(this.Code_Activated);
            this.ResumeLayout(false);
            this.PerformLayout();

		}
		#endregion

		private void Code_Closed(object sender, System.EventArgs e)
		{
			((MainForm)this.ParentForm).RemoveWindowMenu(this.Text);
		}

		private void Code_Activated(object sender, System.EventArgs e)
		{
			txtCode.SelectionStart = txtCode.SelectionLength = 0;
		}

		public string SourceCode
		{
			set
			{
				txtCode.Text = value;
			}
		}

		public string FileName
		{
			get
			{
				return this.Text;
			}
		}



		public void Save()
		{
            //FileStream oFileStream =
            //    new FileStream(GlobalOptions.GetInstance().CurrentProfile.SaveFolder + "\\" + PARTIAL_FILE_PREFIX + Text, 
            //    FileMode.OpenOrCreate, FileAccess.Write);

            TextWriter oTextWriter =
                new StreamWriter(GlobalOptions.GetInstance().CurrentProfile.SaveFolder + "\\" + PARTIAL_FILE_PREFIX + Text.Replace(".cs", PARTIAL_FILE_SURFIX + ".cs")
                , false, Encoding.Unicode);
			oTextWriter.Write(txtCode.Text);
			oTextWriter.Close();
			// oFileStream.Close();
		}

		public void SaveAs(string p_sPath)
		{
			FileStream oFileStream = new FileStream(p_sPath,FileMode.OpenOrCreate,FileAccess.Write);
			TextWriter oTextWriter = new StreamWriter(oFileStream,Encoding.Unicode);
			oTextWriter.Write(txtCode.Text);
			oTextWriter.Close();
			oFileStream.Close();
		}
	}
}
