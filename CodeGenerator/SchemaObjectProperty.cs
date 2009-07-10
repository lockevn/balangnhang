using System;

namespace GURUCORE.GForm.CodeGenerator
{
	public class SchemaObjectProperty
	{
		private string m_sName;
		private string m_sType;
		private bool m_bIdentity;
		private bool m_bReadOnly;
		
		private bool m_bLocalizable;

		public SchemaObjectProperty(string p_sName, string p_sType, bool p_bIdentity, bool p_bReadOnly, bool p_bLocalizable)
		{
			m_sName = p_sName;
			m_sType = p_sType;
			m_bIdentity = p_bIdentity;
			m_bReadOnly = p_bReadOnly;
			m_bLocalizable = p_bLocalizable;
		}

		public string Name
		{
			get
			{
				return m_sName;
			}
		}

		public string Type
		{
			get
			{
				return m_sType;
			}
		}

		public bool Identity
		{
			get
			{
				return m_bIdentity;
			}
		}

		public bool ReadOnly
		{
			get
			{
				return m_bReadOnly;
			}
		}

		public bool Localizable
		{
			get
			{
				return m_bLocalizable;
			}
		}
	}
}
