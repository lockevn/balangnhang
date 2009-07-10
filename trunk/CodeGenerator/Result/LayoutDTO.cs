using System;

using Alphanamict.WAF.DataAccess.ORMapping;

namespace Alphanamict.WAF.CodeGenerator.Result
{
	/// <summary>
	/// Summary description for LayoutDTO.
	/// </summary>
	[PersistenceClass("TblLayout","LayoutID",true)]
	public class LayoutDTO : DTO
	{
		public const string LAYOUTID_FLD = "LayoutID";
		public const string LAYOUTNAME_FLD = "LayoutName";
		public const string DESCRIPTION_FLD = "Description";
		public const string LAYOUTFILE_FLD = "LayoutFile";

		private int m_nLayoutID;
		private string m_sLayoutName;
		private string m_sDescription;
		private string m_sLayoutFile;

		public LayoutDTO()
		{
		}
		
		[PersistenceProperty("LayoutID",true,true)]
		public int LayoutID
		{
			get
			{
				return m_nLayoutID;
			}
			set
			{
				m_nLayoutID = value;
			}
		}
		
		[PersistenceProperty("LayoutName",false,false)]
		public string LayoutName
		{
			get
			{
				return m_sLayoutName;
			}
			set
			{
				m_sLayoutName = value;
			}
		}
		
		[PersistenceProperty("Description",false,false)]
		public string Description
		{
			get
			{
				return m_sDescription;
			}
			set
			{
				m_sDescription = value;
			}
		}
		
		[PersistenceProperty("LayoutFile",false,false)]
		public string LayoutFile
		{
			get
			{
				return m_sLayoutFile;
			}
			set
			{
				m_sLayoutFile = value;
			}
		}
	}
}
