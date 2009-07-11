using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;
using System.Data;
using System.IO;

using NVelocity;
using NVelocity.App;
using NVelocity.Context;

namespace GURUCORE.GForm.CodeGenerator
{	
	public class FormMain : GURUCORE.GForm.Core.FrmBase
	{
		private System.Windows.Forms.ComboBox cboProvider;
		private System.Windows.Forms.ListBox lstObjects;
		private System.Windows.Forms.Button btnGenerate;
		private System.Windows.Forms.Button btnLoad;
		private System.Windows.Forms.Panel pnlStuff;
		private System.Windows.Forms.MenuItem menuItem3;
		private System.Windows.Forms.MainMenu mnuMain;
		private System.Windows.Forms.MenuItem mnuFile;
		private System.Windows.Forms.MenuItem mnuFileExit;
		private System.Windows.Forms.MenuItem mnuFileSaveAll;
		private System.Windows.Forms.MenuItem mnuTool;
		private System.Windows.Forms.MenuItem mnuToolOptions;
		private System.Windows.Forms.MenuItem mnuWindows;
		private System.Windows.Forms.MenuItem mnuHelp;
		private System.Windows.Forms.MenuItem mnuHelpAbout;
		private System.Windows.Forms.MenuItem mnuFileSave;
		private System.Windows.Forms.MenuItem mnuFileSaveAs;
        private System.Windows.Forms.SaveFileDialog dlgSave;
        private ToolStrip toolStrip1;
        private ToolStripButton btnOption;
        private ToolStripButton btnSaveAll;
        private ToolStripButton btnExit;
        private MenuItem mnuToolReloadFromFile;
        private MenuItem mnuToolSaveToFile;
        private ToolStripSeparator toolStripSeparator1;
        private ToolStripSeparator toolStripSeparator2;
        private ToolStripLabel toolStripLabel1;
        private ToolStripComboBox cboProfile;
        private ToolStripDropDownButton btnFileRepository;
        private ToolStripMenuItem tmnuReloadFromFile;
        private ToolStripMenuItem tmnuSaveToFile;
        private ToolTip toolTip;
        private MenuItem mnuFileGenerate;
        private IContainer components;

		public FormMain()
		{
			//
			// Required for Windows Form Designer support
			//
			InitializeComponent();		
		}

		/// <summary>
		/// Clean up any resources being used.
		/// </summary>
		protected override void Dispose( bool disposing )
		{
			if( disposing )
			{
				if (components != null) 
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
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FormMain));
            this.cboProvider = new System.Windows.Forms.ComboBox();
            this.lstObjects = new System.Windows.Forms.ListBox();
            this.btnGenerate = new System.Windows.Forms.Button();
            this.btnLoad = new System.Windows.Forms.Button();
            this.pnlStuff = new System.Windows.Forms.Panel();
            this.mnuMain = new System.Windows.Forms.MainMenu(this.components);
            this.mnuFile = new System.Windows.Forms.MenuItem();
            this.mnuFileSave = new System.Windows.Forms.MenuItem();
            this.mnuFileSaveAs = new System.Windows.Forms.MenuItem();
            this.mnuFileSaveAll = new System.Windows.Forms.MenuItem();
            this.menuItem3 = new System.Windows.Forms.MenuItem();
            this.mnuFileExit = new System.Windows.Forms.MenuItem();
            this.mnuFileGenerate = new System.Windows.Forms.MenuItem();
            this.mnuTool = new System.Windows.Forms.MenuItem();
            this.mnuToolOptions = new System.Windows.Forms.MenuItem();
            this.mnuToolReloadFromFile = new System.Windows.Forms.MenuItem();
            this.mnuToolSaveToFile = new System.Windows.Forms.MenuItem();
            this.mnuWindows = new System.Windows.Forms.MenuItem();
            this.mnuHelp = new System.Windows.Forms.MenuItem();
            this.mnuHelpAbout = new System.Windows.Forms.MenuItem();
            this.dlgSave = new System.Windows.Forms.SaveFileDialog();
            this.toolStrip1 = new System.Windows.Forms.ToolStrip();
            this.toolStripLabel1 = new System.Windows.Forms.ToolStripLabel();
            this.cboProfile = new System.Windows.Forms.ToolStripComboBox();
            this.btnOption = new System.Windows.Forms.ToolStripButton();
            this.btnFileRepository = new System.Windows.Forms.ToolStripDropDownButton();
            this.tmnuReloadFromFile = new System.Windows.Forms.ToolStripMenuItem();
            this.tmnuSaveToFile = new System.Windows.Forms.ToolStripMenuItem();
            this.toolStripSeparator1 = new System.Windows.Forms.ToolStripSeparator();
            this.btnSaveAll = new System.Windows.Forms.ToolStripButton();
            this.toolStripSeparator2 = new System.Windows.Forms.ToolStripSeparator();
            this.btnExit = new System.Windows.Forms.ToolStripButton();
            this.toolTip = new System.Windows.Forms.ToolTip(this.components);
            this.pnlStuff.SuspendLayout();
            this.toolStrip1.SuspendLayout();
            this.SuspendLayout();
            // 
            // btnCancel
            // 
            this.btnCancel.Location = new System.Drawing.Point(781, 381);
            this.btnCancel.Visible = false;
            // 
            // btnOK
            // 
            this.btnOK.Location = new System.Drawing.Point(725, 381);
            this.btnOK.Visible = false;
            // 
            // cboProvider
            // 
            this.cboProvider.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.cboProvider.Items.AddRange(new object[] {
            "SQL Server 2000",
            "SQL Server 2005",
            "My SQL",
            "Postgre SQL",
            "Oracle 9i",
            "Oracle 10g",
            "Sybase",
            "DB2",
            "Firebird"});
            this.cboProvider.Location = new System.Drawing.Point(8, 1);
            this.cboProvider.Name = "cboProvider";
            this.cboProvider.Size = new System.Drawing.Size(200, 22);
            this.cboProvider.TabIndex = 0;
            this.toolTip.SetToolTip(this.cboProvider, "Shortcut key = F3");
            this.cboProvider.SelectedIndexChanged += new System.EventHandler(this.cboProvider_SelectedIndexChanged);
            // 
            // lstObjects
            // 
            this.lstObjects.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom)
                        | System.Windows.Forms.AnchorStyles.Left)));
            this.lstObjects.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.lstObjects.ItemHeight = 14;
            this.lstObjects.Location = new System.Drawing.Point(8, 30);
            this.lstObjects.Name = "lstObjects";
            this.lstObjects.SelectionMode = System.Windows.Forms.SelectionMode.MultiExtended;
            this.lstObjects.Size = new System.Drawing.Size(232, 338);
            this.lstObjects.TabIndex = 1;
            this.lstObjects.TabStop = false;
            this.toolTip.SetToolTip(this.lstObjects, "Shortcut key = F5");
            this.lstObjects.DoubleClick += new System.EventHandler(this.lstObjects_DoubleClick);
            this.lstObjects.KeyUp += new System.Windows.Forms.KeyEventHandler(this.lstObjects_KeyUp);
            // 
            // btnGenerate
            // 
            this.btnGenerate.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.btnGenerate.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(192)))), ((int)(((byte)(0)))), ((int)(((byte)(0)))));
            this.btnGenerate.Font = new System.Drawing.Font("Arial", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.btnGenerate.ForeColor = System.Drawing.Color.White;
            this.btnGenerate.Location = new System.Drawing.Point(154, 384);
            this.btnGenerate.Name = "btnGenerate";
            this.btnGenerate.Size = new System.Drawing.Size(86, 24);
            this.btnGenerate.TabIndex = 5;
            this.btnGenerate.Text = "Generate";
            this.btnGenerate.UseVisualStyleBackColor = false;
            this.btnGenerate.Click += new System.EventHandler(this.btnGenerate_Click);
            // 
            // btnLoad
            // 
            this.btnLoad.Enabled = false;
            this.btnLoad.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnLoad.Location = new System.Drawing.Point(216, 1);
            this.btnLoad.Name = "btnLoad";
            this.btnLoad.Size = new System.Drawing.Size(24, 24);
            this.btnLoad.TabIndex = 7;
            this.btnLoad.Text = "> Go";
            this.btnLoad.Click += new System.EventHandler(this.btnLoad_Click);
            // 
            // pnlStuff
            // 
            this.pnlStuff.Controls.Add(this.btnGenerate);
            this.pnlStuff.Controls.Add(this.lstObjects);
            this.pnlStuff.Controls.Add(this.cboProvider);
            this.pnlStuff.Controls.Add(this.btnLoad);
            this.pnlStuff.Dock = System.Windows.Forms.DockStyle.Left;
            this.pnlStuff.Location = new System.Drawing.Point(0, 0);
            this.pnlStuff.Name = "pnlStuff";
            this.pnlStuff.Size = new System.Drawing.Size(248, 416);
            this.pnlStuff.TabIndex = 10;
            // 
            // mnuMain
            // 
            this.mnuMain.MenuItems.AddRange(new System.Windows.Forms.MenuItem[] {
            this.mnuFile,
            this.mnuTool,
            this.mnuWindows,
            this.mnuHelp});
            // 
            // mnuFile
            // 
            this.mnuFile.Index = 0;
            this.mnuFile.MenuItems.AddRange(new System.Windows.Forms.MenuItem[] {
            this.mnuFileSave,
            this.mnuFileSaveAs,
            this.mnuFileSaveAll,
            this.menuItem3,
            this.mnuFileExit,
            this.mnuFileGenerate});
            this.mnuFile.Text = "&File";
            // 
            // mnuFileSave
            // 
            this.mnuFileSave.Index = 0;
            this.mnuFileSave.Shortcut = System.Windows.Forms.Shortcut.CtrlS;
            this.mnuFileSave.Text = "Save";
            this.mnuFileSave.Click += new System.EventHandler(this.mnuFileSave_Click);
            // 
            // mnuFileSaveAs
            // 
            this.mnuFileSaveAs.Index = 1;
            this.mnuFileSaveAs.Text = "Save As";
            this.mnuFileSaveAs.Click += new System.EventHandler(this.mnuFileSaveAs_Click);
            // 
            // mnuFileSaveAll
            // 
            this.mnuFileSaveAll.Index = 2;
            this.mnuFileSaveAll.Shortcut = System.Windows.Forms.Shortcut.CtrlShiftS;
            this.mnuFileSaveAll.Text = "Save All";
            this.mnuFileSaveAll.Click += new System.EventHandler(this.mnuFileSaveAll_Click);
            // 
            // menuItem3
            // 
            this.menuItem3.Index = 3;
            this.menuItem3.Text = "-";
            // 
            // mnuFileExit
            // 
            this.mnuFileExit.Index = 4;
            this.mnuFileExit.Shortcut = System.Windows.Forms.Shortcut.CtrlX;
            this.mnuFileExit.Text = "E&xit";
            this.mnuFileExit.Click += new System.EventHandler(this.mnuFileExit_Click);
            // 
            // mnuFileGenerate
            // 
            this.mnuFileGenerate.Index = 5;
            this.mnuFileGenerate.Shortcut = System.Windows.Forms.Shortcut.CtrlG;
            this.mnuFileGenerate.Text = "Generate";
            this.mnuFileGenerate.Click += new System.EventHandler(this.mnuFileGenerate_Click);
            // 
            // mnuTool
            // 
            this.mnuTool.Index = 1;
            this.mnuTool.MenuItems.AddRange(new System.Windows.Forms.MenuItem[] {
            this.mnuToolOptions,
            this.mnuToolReloadFromFile,
            this.mnuToolSaveToFile});
            this.mnuTool.Text = "&Tool";
            // 
            // mnuToolOptions
            // 
            this.mnuToolOptions.Index = 0;
            this.mnuToolOptions.Shortcut = System.Windows.Forms.Shortcut.F10;
            this.mnuToolOptions.Text = "Options";
            this.mnuToolOptions.Click += new System.EventHandler(this.mnuToolOptions_Click);
            // 
            // mnuToolReloadFromFile
            // 
            this.mnuToolReloadFromFile.Index = 1;
            this.mnuToolReloadFromFile.Shortcut = System.Windows.Forms.Shortcut.CtrlShiftF10;
            this.mnuToolReloadFromFile.Text = "Reload from File";
            this.mnuToolReloadFromFile.Click += new System.EventHandler(this.mnuToolReloadFromFile_Click);
            // 
            // mnuToolSaveToFile
            // 
            this.mnuToolSaveToFile.Index = 2;
            this.mnuToolSaveToFile.Shortcut = System.Windows.Forms.Shortcut.CtrlF10;
            this.mnuToolSaveToFile.Text = "Save to File";
            this.mnuToolSaveToFile.Click += new System.EventHandler(this.mnuToolSaveToFile_Click);
            // 
            // mnuWindows
            // 
            this.mnuWindows.Index = 2;
            this.mnuWindows.Text = "&Windows";
            // 
            // mnuHelp
            // 
            this.mnuHelp.Index = 3;
            this.mnuHelp.MenuItems.AddRange(new System.Windows.Forms.MenuItem[] {
            this.mnuHelpAbout});
            this.mnuHelp.Text = "Help";
            // 
            // mnuHelpAbout
            // 
            this.mnuHelpAbout.Index = 0;
            this.mnuHelpAbout.Shortcut = System.Windows.Forms.Shortcut.F1;
            this.mnuHelpAbout.Text = "About";
            this.mnuHelpAbout.Click += new System.EventHandler(this.mnuHelpAbout_Click);
            // 
            // toolStrip1
            // 
            this.toolStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.toolStripLabel1,
            this.cboProfile,
            this.btnOption,
            this.btnFileRepository,
            this.toolStripSeparator1,
            this.btnSaveAll,
            this.toolStripSeparator2,
            this.btnExit});
            this.toolStrip1.Location = new System.Drawing.Point(248, 0);
            this.toolStrip1.Name = "toolStrip1";
            this.toolStrip1.Size = new System.Drawing.Size(601, 25);
            this.toolStrip1.TabIndex = 12;
            this.toolStrip1.Text = "Toolbar";
            // 
            // toolStripLabel1
            // 
            this.toolStripLabel1.Name = "toolStripLabel1";
            this.toolStripLabel1.Size = new System.Drawing.Size(44, 22);
            this.toolStripLabel1.Text = "Profile: ";
            this.toolStripLabel1.ToolTipText = "Shortcut key = F4";
            // 
            // cboProfile
            // 
            this.cboProfile.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.cboProfile.DropDownWidth = 121;
            this.cboProfile.Name = "cboProfile";
            this.cboProfile.Size = new System.Drawing.Size(121, 25);
            this.cboProfile.Sorted = true;
            this.cboProfile.ToolTipText = "Shortcut key = F4";
            this.cboProfile.SelectedIndexChanged += new System.EventHandler(this.cboProfile_SelectedIndexChanged);
            // 
            // btnOption
            // 
            this.btnOption.Image = ((System.Drawing.Image)(resources.GetObject("btnOption.Image")));
            this.btnOption.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.btnOption.Name = "btnOption";
            this.btnOption.Size = new System.Drawing.Size(59, 22);
            this.btnOption.Text = "Option";
            this.btnOption.ToolTipText = "Show the profile Option";
            this.btnOption.Click += new System.EventHandler(this.mnuToolOptions_Click);
            // 
            // btnFileRepository
            // 
            this.btnFileRepository.AutoToolTip = false;
            this.btnFileRepository.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.tmnuReloadFromFile,
            this.tmnuSaveToFile});
            this.btnFileRepository.Image = ((System.Drawing.Image)(resources.GetObject("btnFileRepository.Image")));
            this.btnFileRepository.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.btnFileRepository.Name = "btnFileRepository";
            this.btnFileRepository.Size = new System.Drawing.Size(84, 22);
            this.btnFileRepository.Text = "Config file";
            // 
            // tmnuReloadFromFile
            // 
            this.tmnuReloadFromFile.Name = "tmnuReloadFromFile";
            this.tmnuReloadFromFile.Size = new System.Drawing.Size(162, 22);
            this.tmnuReloadFromFile.Text = "Reload from File";
            this.tmnuReloadFromFile.Click += new System.EventHandler(this.mnuToolReloadFromFile_Click);
            // 
            // tmnuSaveToFile
            // 
            this.tmnuSaveToFile.Name = "tmnuSaveToFile";
            this.tmnuSaveToFile.Size = new System.Drawing.Size(162, 22);
            this.tmnuSaveToFile.Text = "Save to File";
            this.tmnuSaveToFile.Click += new System.EventHandler(this.mnuToolSaveToFile_Click);
            // 
            // toolStripSeparator1
            // 
            this.toolStripSeparator1.Name = "toolStripSeparator1";
            this.toolStripSeparator1.Size = new System.Drawing.Size(6, 25);
            // 
            // btnSaveAll
            // 
            this.btnSaveAll.Image = ((System.Drawing.Image)(resources.GetObject("btnSaveAll.Image")));
            this.btnSaveAll.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.btnSaveAll.Name = "btnSaveAll";
            this.btnSaveAll.Size = new System.Drawing.Size(64, 22);
            this.btnSaveAll.Text = "Save all";
            this.btnSaveAll.ToolTipText = "Save all generated code to files on disk";
            this.btnSaveAll.Click += new System.EventHandler(this.mnuFileSaveAll_Click);
            // 
            // toolStripSeparator2
            // 
            this.toolStripSeparator2.Name = "toolStripSeparator2";
            this.toolStripSeparator2.Size = new System.Drawing.Size(6, 25);
            // 
            // btnExit
            // 
            this.btnExit.Image = ((System.Drawing.Image)(resources.GetObject("btnExit.Image")));
            this.btnExit.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.btnExit.Name = "btnExit";
            this.btnExit.Size = new System.Drawing.Size(45, 22);
            this.btnExit.Text = "Exit";
            this.btnExit.ToolTipText = "Save config file, and quit";
            this.btnExit.Click += new System.EventHandler(this.mnuFileExit_Click);
            // 
            // toolTip
            // 
            this.toolTip.AutoPopDelay = 3000;
            this.toolTip.InitialDelay = 200;
            this.toolTip.ReshowDelay = 100;
            this.toolTip.ShowAlways = true;
            this.toolTip.UseAnimation = false;
            this.toolTip.UseFading = false;
            // 
            // MainForm
            // 
            this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
            this.ClientSize = new System.Drawing.Size(849, 416);
            this.Controls.Add(this.toolStrip1);
            this.Controls.Add(this.pnlStuff);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.IsMdiContainer = true;
            this.KeyPreview = true;
            this.Menu = this.mnuMain;
            this.Name = "MainForm";
            this.Text = "GURUCORE (2007) DTO Generator for Gabriella FR30";
            this.WindowState = System.Windows.Forms.FormWindowState.Maximized;
            this.PreviewKeyDown += new System.Windows.Forms.PreviewKeyDownEventHandler(this.MainForm_PreviewKeyDown);
            this.Closed += new System.EventHandler(this.MainForm_Closed);
            this.KeyUp += new System.Windows.Forms.KeyEventHandler(this.MainForm_KeyUp);
            this.Load += new System.EventHandler(this.MainForm_Load);
            this.Controls.SetChildIndex(this.btnCancel, 0);
            this.Controls.SetChildIndex(this.btnOK, 0);
            this.Controls.SetChildIndex(this.pnlStuff, 0);
            this.Controls.SetChildIndex(this.toolStrip1, 0);
            this.pnlStuff.ResumeLayout(false);
            this.toolStrip1.ResumeLayout(false);
            this.toolStrip1.PerformLayout();
            this.ResumeLayout(false);
            this.PerformLayout();

		}
		#endregion
        	
		[STAThread]
		static void Main() 
		{
            // any case, dumb the Profiles in the program to file()
            try
            {
                Application.Run(new FormMain());
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
                GlobalOptions.GetInstance().WriteFileRepository();
            }
		}



		private ArrayList m_arrSchemaObjects;
		private VelocityContext m_oVelocityCtx;

		private void LoadObjects()
		{
            FormConnect frmConnect;
			switch (cboProvider.SelectedIndex)
			{
				case 0:
				{
					frmConnect = new FormConnectSqlServer();
					break;
				}
                case 1:
                {
                    frmConnect = new FormConnectSqlServer();
                    break;
                }
                case 2:
                {
                    frmConnect = new FormConnectMySql();
                    break;
                }
				default:
				{
					frmConnect = null;
					break;
				}
			}

			lstObjects.Items.Clear();
			foreach (FormCode frmCode in this.MdiChildren)
			{
				frmCode.Close();
			}

			if ((frmConnect != null) && (frmConnect.ShowDialog(this) == DialogResult.OK))
			{
				SchemaReader oSchemaReader = frmConnect.SchemaReader;
				m_arrSchemaObjects = oSchemaReader.GetSchemaObjects();
				lstObjects.DataSource = m_arrSchemaObjects;
				lstObjects.DisplayMember = lstObjects.ValueMember = "Name";
				lstObjects.SelectedIndex = 0;
			}
		}

        private FormCode FindCodeForm(string p_sName)
        {
            foreach (FormCode frmCode in this.MdiChildren)
            {
                if (frmCode.FileName == p_sName)
                {
                    return frmCode;
                }
            }
            MenuItem mnuCodeWindow = new MenuItem(p_sName);
            // mnuCodeWindow.Click += new EventHandler(mnuCodeWindow_Click);
            mnuWindows.MenuItems.Add(mnuCodeWindow);
            return new FormCode(p_sName);
        }

        public void RemoveWindowMenu(string p_sName)
        {
            foreach (MenuItem mnuCodeWindow in mnuWindows.MenuItems)
            {
                if (mnuCodeWindow.Text == p_sName)
                {
                    mnuWindows.MenuItems.Remove(mnuCodeWindow);
                    return;
                }
            }
        }

        private string GenCode(SchemaObject p_oSchemaObject)
        {
            m_oVelocityCtx.Put("namespace", GlobalOptions.GetInstance().CurrentProfile.DefaultNameSpace);
            m_oVelocityCtx.Put("schemaObject", p_oSchemaObject);
            m_oVelocityCtx.Put("tool", new TemplateTool(GlobalOptions.GetInstance().CurrentProfile.TablePrefixes));

            Directory.SetCurrentDirectory(new System.IO.FileInfo(System.Reflection.Assembly.GetEntryAssembly().Location).Directory.FullName);

            Template oTpl = Velocity.GetTemplate("DTO.tpl");
            TextWriter oWriter = new StringWriter();
            oTpl.Merge(m_oVelocityCtx, oWriter);
            return oWriter.ToString();
        }





		private void cboProvider_SelectedIndexChanged(object sender, System.EventArgs e)
		{
			LoadObjects();
		}

		private void btnLoad_Click(object sender, System.EventArgs e)
		{
			LoadObjects();
		}



		private void btnGenerate_Click(object sender, System.EventArgs e)
		{
			foreach (int nIdx in lstObjects.SelectedIndices)
			{
				string sSourceCode = GenCode((SchemaObject)m_arrSchemaObjects[nIdx]);


				//find form
                FormCode frmCode = FindCodeForm((new TemplateTool(GlobalOptions.GetInstance().CurrentProfile.TablePrefixes)).TruncatePrefix(((SchemaObject)m_arrSchemaObjects[nIdx]).Name) + "DTO.cs");
				frmCode.MdiParent = this;
				frmCode.SourceCode = sSourceCode;
				frmCode.Show();
				frmCode.Activate();
			}
		}
        		
		private void MainForm_Load(object sender, System.EventArgs e)
		{		
			Velocity.Init();
			m_oVelocityCtx = new VelocityContext();            
			GlobalOptions.GetInstance().ReadFileRepository();

            foreach (string s in GlobalOptions.GetInstance().GetProfileList())
            {
                cboProfile.Items.Add(s);
            }
            cboProfile.SelectedIndex = 0;
			// Option frmOption = new Option();
			// frmOption.ShowDialog(this);
        }

		private void lstObjects_DoubleClick(object sender, System.EventArgs e)
		{		
			string sSourceCode = GenCode((SchemaObject)m_arrSchemaObjects[lstObjects.SelectedIndex]);

			//find form
            FormCode frmCode = FindCodeForm((new TemplateTool(GlobalOptions.GetInstance().CurrentProfile.TablePrefixes)).TruncatePrefix(((SchemaObject)m_arrSchemaObjects[lstObjects.SelectedIndex]).Name) + "DTO.cs");
			frmCode.MdiParent = this;
			frmCode.SourceCode = sSourceCode;
			frmCode.Show();
			frmCode.Activate();
		}

		private void mnuFileSave_Click(object sender, System.EventArgs e)
		{
			FormCode frmCode = (FormCode)this.ActiveMdiChild;
			frmCode.Save();
		}

		private void mnuToolOptions_Click(object sender, System.EventArgs e)
		{
			FormOption frmOption = new FormOption();
			frmOption.ShowDialog(this);
		}

		private void mnuFileSaveAs_Click(object sender, System.EventArgs e)
		{
			FormCode frmCode = (FormCode)this.ActiveMdiChild;
            dlgSave.FileName = GlobalOptions.GetInstance().CurrentProfile.SaveFolder + "\\" + frmCode.Text;
			if (dlgSave.ShowDialog(this) == DialogResult.OK)
			{
				frmCode.SaveAs(dlgSave.FileName);
			}
		}

		private void mnuFileSaveAll_Click(object sender, System.EventArgs e)
		{
			foreach (FormCode frmCode in MdiChildren)
			{
				frmCode.Save();
			}
		}

		private void mnuFileExit_Click(object sender, System.EventArgs e)
		{
			this.Close();
		}

		private void MainForm_Closed(object sender, System.EventArgs e)
		{
			GlobalOptions.GetInstance().WriteFileRepository();
		}

        private void mnuToolReloadFromFile_Click(object sender, EventArgs e)
        {
            GlobalOptions.GetInstance().ReadFileRepository();
        }

        private void mnuToolSaveToFile_Click(object sender, EventArgs e)
        {
            GlobalOptions.GetInstance().WriteFileRepository();
        }

        private void mnuHelpAbout_Click(object sender, EventArgs e)
        {
            FormAboutBox frm = new FormAboutBox();
            frm.ShowDialog(this);
        }

        private void cboProfile_SelectedIndexChanged(object sender, EventArgs e)
        {
            GlobalOptions.GetInstance().CurrentProfileName = cboProfile.SelectedItem.ToString();
        }

        private void MainForm_PreviewKeyDown(object sender, PreviewKeyDownEventArgs e)
        {
            
        }


        private void MainForm_KeyUp(object sender, KeyEventArgs e)
        {
            if (e.KeyCode == Keys.F3)
            {
                cboProvider.Focus();
            }
            else if (e.KeyCode == Keys.F4)
            {
                cboProfile.Focus();
            }
            else if (e.KeyCode == Keys.F5)
            {
                lstObjects.Focus();
            }
        }

        private void lstObjects_KeyUp(object sender, KeyEventArgs e)
        {
            if (e.KeyCode == Keys.Enter)
            {
                lstObjects_DoubleClick(sender, e);
            }
        }
        private void mnuFileGenerate_Click(object sender, EventArgs e)
        {
            btnGenerate_Click(sender, e);
        }        

	}
}
